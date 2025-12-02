<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService
    ) {
        $this->middleware(['auth', 'role:admin|campaign_manager']);
    }

    /**
     * Display the admin dashboard.
     */
    public function index(): Response
    {
        $metrics = $this->analyticsService->getDashboardMetrics();
        $topCampaigns = $this->analyticsService->getTopCampaigns(5);
        $recentDonations = $this->analyticsService->getRecentDonations(5);
        $campaignsByCategory = $this->analyticsService->getCampaignsByCategory();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $metrics,
            'recentCampaigns' => $topCampaigns,
            'recentDonations' => $recentDonations,
            'campaignsByCategory' => $campaignsByCategory,
            'topCampaigns' => $topCampaigns,
        ]);
    }

    public function analytics(Request $request): Response
    {
        $range = $request->input('range', 30); // Default to 30 days
        $validated = $request->validate([
            'range' => 'integer|min:7|max:365',
        ]);

        $analytics = $this->analyticsService->getAdvancedAnalytics($range);

        return Inertia::render('Admin/Analytics', [
            'analytics' => $analytics,
            'selectedRange' => $range,
        ]);
    }
}
