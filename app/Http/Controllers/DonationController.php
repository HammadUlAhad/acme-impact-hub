<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        $donations = Donation::with(['campaign'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_donations' => $donations->where('payment_status', Donation::STATUS_COMPLETED)->count(),
            'total_amount' => $donations->where('payment_status', Donation::STATUS_COMPLETED)->sum('amount'),
            'pending_donations' => $donations->where('payment_status', Donation::STATUS_PENDING)->count(),
        ];

        return Inertia::render('Donations/Index', [
            'donations' => $donations,
            'stats' => $stats,
        ]);
    }

    public function create(Campaign $campaign): Response
    {
        if (!$campaign->isAcceptingDonations()) {
            abort(403, 'This campaign is not accepting donations.');
        }

        $userPreviousDonations = $campaign->donations()
            ->where('user_id', Auth::id())
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->sum('amount');

        return Inertia::render('Donations/Create', [
            'campaign' => $campaign,
            'userPreviousDonations' => $userPreviousDonations,
            'paymentMethods' => Donation::getPaymentMethods(),
        ]);
    }

    public function store(Request $request, Campaign $campaign): RedirectResponse
    {
        if (!$campaign->isAcceptingDonations()) {
            return back()->withErrors(['campaign' => 'This campaign is not accepting donations.']);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'payment_method' => 'required|in:' . implode(',', array_keys(Donation::getPaymentMethods())),
            'is_anonymous' => 'boolean',
            'donor_message' => 'nullable|string|max:500',
        ]);

        // Create donation record
        $donation = Donation::create([
            'user_id' => Auth::id(),
            'campaign_id' => $campaign->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'donor_message' => $request->donor_message,
            'payment_status' => Donation::STATUS_PENDING,
        ]);

        // Process payment based on method
        $this->processPayment($donation);

        return redirect()->route('donations.show', $donation)
            ->with('success', 'Donation submitted successfully!');
    }

    public function show(Donation $donation): Response
    {
        // Ensure user can only view their own donations or admins can view all
        if ($donation->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'campaign_manager'])) {
            abort(403);
        }

        $donation->load(['user', 'campaign']);

        return Inertia::render('Donations/Show', [
            'donation' => $donation,
        ]);
    }

    private function processPayment(Donation $donation): void
    {
        DB::transaction(function () use ($donation) {
            switch ($donation->payment_method) {
                case Donation::METHOD_PAYROLL_DEDUCTION:
                    $this->processPayrollDeduction($donation);
                    break;
                case Donation::METHOD_BANK_TRANSFER:
                    $this->processBankTransfer($donation);
                    break;
                case Donation::METHOD_CREDIT_CARD:
                    $this->processCreditCard($donation);
                    break;
                case Donation::METHOD_DIGITAL_WALLET:
                    $this->processDigitalWallet($donation);
                    break;
            }
        });
    }

    private function processPayrollDeduction(Donation $donation): void
    {
        // For payroll deduction, we auto-approve since it's internal
        $donation->update([
            'payment_status' => Donation::STATUS_COMPLETED,
            'processed_at' => now(),
            'payment_reference' => 'PAYROLL_' . now()->format('YmdHis') . '_' . $donation->id,
        ]);

        // Update campaign current amount
        $donation->campaign->increment('current_amount', $donation->amount);
    }

    private function processBankTransfer(Donation $donation): void
    {
        // Bank transfer requires manual verification
        $donation->update([
            'payment_status' => Donation::STATUS_PROCESSING,
            'payment_reference' => 'BANK_' . now()->format('YmdHis') . '_' . $donation->id,
        ]);
    }

    private function processCreditCard(Donation $donation): void
    {
        // TODO: Integrate with actual payment gateway
        // For now, we'll simulate successful payment
        $donation->update([
            'payment_status' => Donation::STATUS_COMPLETED,
            'processed_at' => now(),
            'payment_reference' => 'CC_' . now()->format('YmdHis') . '_' . $donation->id,
        ]);

        // Update campaign current amount
        $donation->campaign->increment('current_amount', $donation->amount);
    }

    private function processDigitalWallet(Donation $donation): void
    {
        // TODO: Integrate with digital wallet providers
        // For now, we'll simulate successful payment
        $donation->update([
            'payment_status' => Donation::STATUS_COMPLETED,
            'processed_at' => now(),
            'payment_reference' => 'DW_' . now()->format('YmdHis') . '_' . $donation->id,
        ]);

        // Update campaign current amount
        $donation->campaign->increment('current_amount', $donation->amount);
    }

    public function approve(Donation $donation): RedirectResponse
    {
        if (!Auth::user()->hasRole(['admin', 'campaign_manager'])) {
            abort(403);
        }

        if ($donation->payment_status !== Donation::STATUS_PROCESSING) {
            return back()->withErrors(['donation' => 'Only processing donations can be approved.']);
        }

        $donation->update([
            'payment_status' => Donation::STATUS_COMPLETED,
            'processed_at' => now(),
        ]);

        // Update campaign current amount
        $donation->campaign->increment('current_amount', $donation->amount);

        return back()->with('success', 'Donation approved successfully.');
    }

    public function refund(Request $request, Donation $donation): RedirectResponse
    {
        if (!Auth::user()->hasRole(['admin', 'campaign_manager'])) {
            abort(403);
        }

        if ($donation->payment_status !== Donation::STATUS_COMPLETED) {
            return back()->withErrors(['donation' => 'Only completed donations can be refunded.']);
        }

        $request->validate([
            'refund_reason' => 'required|string|max:500',
        ]);

        $donation->update([
            'payment_status' => Donation::STATUS_REFUNDED,
            'refunded_at' => now(),
            'refund_reason' => $request->refund_reason,
        ]);

        // Decrease campaign current amount
        $donation->campaign->decrement('current_amount', $donation->amount);

        return back()->with('success', 'Donation refunded successfully.');
    }
}
