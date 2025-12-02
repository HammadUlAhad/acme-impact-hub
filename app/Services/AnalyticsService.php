<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AnalyticsService
{
    /**
     * Get platform statistics.
     */
    public function getPlatformStats(): array
    {
        return [
            'total_campaigns' => Campaign::count(),
            'active_campaigns' => Campaign::where('status', Campaign::STATUS_ACTIVE)->count(),
            'pending_campaigns' => Campaign::where('status', Campaign::STATUS_PENDING)->count(),
            'total_donations' => Donation::where('payment_status', Donation::STATUS_COMPLETED)->count(),
            'total_donation_amount' => Donation::where('payment_status', Donation::STATUS_COMPLETED)->sum('amount'),
            'total_employees' => User::where('is_active', true)->count(),
            'active_donors' => Donation::where('payment_status', Donation::STATUS_COMPLETED)
                ->distinct('user_id')->count('user_id'),
        ];
    }

    /**
     * Get recent campaigns.
     */
    public function getRecentCampaigns(int $limit = 10): Collection
    {
        return Campaign::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent donations.
     */
    public function getRecentDonations(int $limit = 10): Collection
    {
        return Donation::with(['user', 'campaign'])
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get campaigns by category.
     */
    public function getCampaignsByCategory(): Collection
    {
        return Campaign::selectRaw('cause_category, COUNT(*) as count, SUM(current_amount) as total_raised')
            ->where('status', Campaign::STATUS_ACTIVE)
            ->groupBy('cause_category')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get top performing campaigns.
     */
    public function getTopCampaigns(int $limit = 10): Collection
    {
        return Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->orderBy('current_amount', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get donation trends by period.
     */
    public function getDonationTrends(string $period = 'month'): Collection
    {
        $dateFormat = match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };

        return Donation::selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, COUNT(*) as count, SUM(amount) as total")
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    /**
     * Get user engagement metrics.
     */
    public function getUserEngagementMetrics(): array
    {
        $totalUsers = User::where('is_active', true)->count();
        $donorUsers = Donation::where('payment_status', Donation::STATUS_COMPLETED)
            ->distinct('user_id')->count('user_id');
        $campaignCreators = Campaign::distinct('created_by')->count('created_by');

        return [
            'total_users' => $totalUsers,
            'donor_users' => $donorUsers,
            'campaign_creators' => $campaignCreators,
            'donor_participation_rate' => $totalUsers > 0 ? ($donorUsers / $totalUsers) * 100 : 0,
            'creator_participation_rate' => $totalUsers > 0 ? ($campaignCreators / $totalUsers) * 100 : 0,
        ];
    }
}