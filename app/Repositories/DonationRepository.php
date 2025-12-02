<?php

namespace App\Repositories;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use App\Repositories\Contracts\DonationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DonationRepository implements DonationRepositoryInterface
{
    public function findById(int $id): ?Donation
    {
        return Donation::with(['user', 'campaign'])->find($id);
    }
    
    public function create(array $data): Donation
    {
        return Donation::create($data);
    }
    
    public function update(Donation $donation, array $data): bool
    {
        return $donation->update($data);
    }
    
    public function delete(Donation $donation): bool
    {
        return $donation->delete();
    }
    
    public function getByUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Donation::with(['campaign'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getByCampaign(Campaign $campaign, int $perPage = 15): LengthAwarePaginator
    {
        return Donation::with(['user'])
            ->where('campaign_id', $campaign->id)
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getRecent(int $limit = 10): Collection
    {
        return Donation::with(['user', 'campaign'])
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getPending(): Collection
    {
        return Donation::with(['user', 'campaign'])
            ->where('payment_status', Donation::STATUS_PENDING)
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    public function getProcessing(): Collection
    {
        return Donation::with(['user', 'campaign'])
            ->where('payment_status', Donation::STATUS_PROCESSING)
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    public function getCompleted(): Collection
    {
        return Donation::with(['user', 'campaign'])
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function getTotalAmount(): float
    {
        return (float) Donation::where('payment_status', Donation::STATUS_COMPLETED)->sum('amount');
    }
    
    public function getTotalCount(): int
    {
        return Donation::where('payment_status', Donation::STATUS_COMPLETED)->count();
    }
    
    public function getCompletedCount(): int
    {
        return Donation::where('payment_status', Donation::STATUS_COMPLETED)->count();
    }
    
    public function getPendingCount(): int
    {
        return Donation::where('payment_status', Donation::STATUS_PENDING)->count();
    }
    
    public function getUserTotalDonations(User $user): float
    {
        return (float) Donation::where('user_id', $user->id)
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->sum('amount');
    }
    
    public function getUserDonationCount(User $user): int
    {
        return Donation::where('user_id', $user->id)
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->count();
    }
    
    public function getUserCompletedDonationCount(User $user): int
    {
        return Donation::where('user_id', $user->id)
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->count();
    }
    
    public function getUserPendingDonationCount(User $user): int
    {
        return Donation::where('user_id', $user->id)
            ->where('payment_status', Donation::STATUS_PENDING)
            ->count();
    }
    
    public function getUserDonationsToCampaign(User $user, Campaign $campaign): float
    {
        return (float) Donation::where('user_id', $user->id)
            ->where('campaign_id', $campaign->id)
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->sum('amount');
    }
    
    public function getDonationTrends(int $days = 30): Collection
    {
        return Donation::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(amount) as amount')
        )
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    public function getPaymentMethodBreakdown(int $days = 30): Collection
    {
        return Donation::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as amount'))
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('payment_method')
            ->get();
    }
    
    public function getDonationsByDateRange(\DateTime $startDate, \DateTime $endDate): Collection
    {
        return Donation::with(['user', 'campaign'])
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}