<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use App\Services\Payment\PaymentProcessorFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DonationService
{
    public function __construct(
        private PaymentProcessorFactory $paymentProcessorFactory
    ) {}

    /**
     * Get user donations with pagination.
     */
    public function getUserDonations(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Donation::with(['campaign'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get donation statistics for a user.
     */
    public function getUserDonationStats(User $user): array
    {
        $donations = Donation::where('user_id', $user->id);

        return [
            'total_donations' => $donations->where('payment_status', Donation::STATUS_COMPLETED)->count(),
            'total_amount' => $donations->where('payment_status', Donation::STATUS_COMPLETED)->sum('amount'),
            'pending_donations' => $donations->where('payment_status', Donation::STATUS_PENDING)->count(),
        ];
    }

    /**
     * Create a new donation.
     */
    public function createDonation(Campaign $campaign, User $user, array $data): Donation
    {
        if (!$campaign->isAcceptingDonations()) {
            throw new \InvalidArgumentException('This campaign is not accepting donations.');
        }

        return DB::transaction(function () use ($campaign, $user, $data) {
            $donation = Donation::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'is_anonymous' => $data['is_anonymous'] ?? false,
                'donor_message' => $data['donor_message'] ?? null,
                'payment_status' => Donation::STATUS_PENDING,
            ]);

            // Process payment
            $this->processPayment($donation);

            return $donation->fresh();
        });
    }

    /**
     * Process payment for a donation.
     */
    public function processPayment(Donation $donation): void
    {
        $processor = $this->paymentProcessorFactory->create($donation->payment_method);
        $processor->process($donation);
    }

    /**
     * Approve a donation (for manual verification methods like bank transfer).
     */
    public function approveDonation(Donation $donation): Donation
    {
        if ($donation->payment_status !== Donation::STATUS_PROCESSING) {
            throw new \InvalidArgumentException('Only processing donations can be approved.');
        }

        return DB::transaction(function () use ($donation) {
            $donation->update([
                'payment_status' => Donation::STATUS_COMPLETED,
                'processed_at' => now(),
            ]);

            // Update campaign current amount
            $donation->campaign->increment('current_amount', $donation->amount);

            return $donation->fresh();
        });
    }

    /**
     * Refund a donation.
     */
    public function refundDonation(Donation $donation, string $reason): Donation
    {
        if ($donation->payment_status !== Donation::STATUS_COMPLETED) {
            throw new \InvalidArgumentException('Only completed donations can be refunded.');
        }

        return DB::transaction(function () use ($donation, $reason) {
            $donation->update([
                'payment_status' => Donation::STATUS_REFUNDED,
                'refunded_at' => now(),
                'refund_reason' => $reason,
            ]);

            // Decrease campaign current amount
            $donation->campaign->decrement('current_amount', $donation->amount);

            return $donation->fresh();
        });
    }

    /**
     * Get user's previous donations to a campaign.
     */
    public function getUserPreviousDonations(Campaign $campaign, User $user): float
    {
        return $campaign->donations()
            ->where('user_id', $user->id)
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->sum('amount');
    }
}