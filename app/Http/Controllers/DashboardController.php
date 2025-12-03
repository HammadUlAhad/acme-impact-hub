<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        $user = Auth::user();
        
        // Get user's personal stats
        $userStats = [
            'total_donations' => Donation::where('user_id', $user->id)
                ->where('payment_status', Donation::STATUS_COMPLETED)
                ->count(),
            'total_donated' => Donation::where('user_id', $user->id)
                ->where('payment_status', Donation::STATUS_COMPLETED)
                ->sum('amount'),
            'campaigns_created' => Campaign::where('creator_id', $user->id)->count(),
            'active_campaigns' => Campaign::where('creator_id', $user->id)
                ->where('status', Campaign::STATUS_ACTIVE)
                ->count(),
        ];

        // Get featured campaigns
        $featuredCampaigns = Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->where('featured', true)
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Get recent donations from the user
        $recentDonations = Donation::with(['campaign'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get platform-wide stats for context
        $platformStats = [
            'total_campaigns' => Campaign::count(),
            'active_campaigns' => Campaign::where('status', Campaign::STATUS_ACTIVE)->count(),
            'total_raised' => Donation::where('payment_status', Donation::STATUS_COMPLETED)->sum('amount'),
            'total_donors' => Donation::where('payment_status', Donation::STATUS_COMPLETED)
                ->distinct('user_id')->count('user_id'),
        ];

        return Inertia::render('Dashboard', [
            'userStats' => $userStats,
            'featuredCampaigns' => $featuredCampaigns,
            'recentDonations' => $recentDonations,
            'platformStats' => $platformStats,
        ]);
    }
}