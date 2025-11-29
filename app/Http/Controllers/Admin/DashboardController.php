<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|campaign_manager']);
    }

    /**
     * Display the admin dashboard.
     */
    public function index(): Response
    {
        // Get platform statistics
        $stats = [
            'total_campaigns' => Campaign::count(),
            'active_campaigns' => Campaign::where('status', Campaign::STATUS_ACTIVE)->count(),
            'pending_campaigns' => Campaign::where('status', Campaign::STATUS_PENDING)->count(),
            'total_donations' => Donation::where('payment_status', Donation::STATUS_COMPLETED)->count(),
            'total_donation_amount' => Donation::where('payment_status', Donation::STATUS_COMPLETED)->sum('amount'),
            'total_employees' => User::where('is_active', true)->count(),
            'active_donors' => User::whereHas('donations', function($query) {
                $query->where('payment_status', Donation::STATUS_COMPLETED);
            })->count(),
        ];

        // Get recent campaigns
        $recentCampaigns = Campaign::with(['creator', 'approver'])
            ->latest()
            ->limit(5)
            ->get();

        // Get recent donations
        $recentDonations = Donation::with(['user', 'campaign'])
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->latest()
            ->limit(5)
            ->get();

        // Get campaigns by category
        $campaignsByCategory = Campaign::selectRaw('cause_category, COUNT(*) as count, SUM(current_amount) as total_raised')
            ->where('status', Campaign::STATUS_ACTIVE)
            ->groupBy('cause_category')
            ->get();

        // Get top performing campaigns
        $topCampaigns = Campaign::where('status', Campaign::STATUS_ACTIVE)
            ->orderBy('current_amount', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentCampaigns' => $recentCampaigns,
            'recentDonations' => $recentDonations,
            'campaignsByCategory' => $campaignsByCategory,
            'topCampaigns' => $topCampaigns,
        ]);
    }
}
