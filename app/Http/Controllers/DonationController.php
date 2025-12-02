<?php

namespace App\Http\Controllers;

use App\Http\Requests\Donation\StoreDonationRequest;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\DonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DonationController extends Controller
{
    public function __construct(
        private DonationService $donationService
    ) {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        $donations = $this->donationService->getUserDonations(Auth::user(), 15);
        $stats = $this->donationService->getUserDonationStats(Auth::user());

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

        $userPreviousDonations = $this->donationService->getUserPreviousDonations($campaign, Auth::user());

        return Inertia::render('Donations/Create', [
            'campaign' => $campaign,
            'userPreviousDonations' => $userPreviousDonations,
            'paymentMethods' => Donation::getPaymentMethods(),
        ]);
    }

    public function store(StoreDonationRequest $request, Campaign $campaign): RedirectResponse
    {
        try {
            $donation = $this->donationService->createDonation(
                $campaign, 
                Auth::user(), 
                $request->validated()
            );

            return redirect()->route('donations.show', $donation)
                ->with('success', 'Donation submitted successfully!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['campaign' => $e->getMessage()]);
        }
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

    public function approve(Donation $donation): RedirectResponse
    {
        if (!Auth::user()->hasRole(['admin', 'campaign_manager'])) {
            abort(403);
        }

        try {
            $this->donationService->approveDonation($donation);
            return back()->with('success', 'Donation approved successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['donation' => $e->getMessage()]);
        }
    }

    public function refund(Request $request, Donation $donation): RedirectResponse
    {
        if (!Auth::user()->hasRole(['admin', 'campaign_manager'])) {
            abort(403);
        }

        $request->validate([
            'refund_reason' => 'required|string|max:500',
        ]);

        try {
            $this->donationService->refundDonation($donation, $request->refund_reason);
            return back()->with('success', 'Donation refunded successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['donation' => $e->getMessage()]);
        }
    }
}
