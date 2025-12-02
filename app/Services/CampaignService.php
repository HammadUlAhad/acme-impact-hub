<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CampaignService
{
    /**
     * Get campaigns with filters and pagination.
     */
    public function getCampaigns(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Campaign::with(['creator', 'donations'])
            ->when($filters['search'] ?? null, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, function($query, $category) {
                $query->where('cause_category', $category);
            })
            ->when($filters['status'] ?? null, function($query, $status) {
                $query->where('status', $status);
            })
            ->where('status', '!=', Campaign::STATUS_DRAFT)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Create a new campaign.
     */
    public function createCampaign(array $data, User $user): Campaign
    {
        $campaignData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'cause_category' => $data['cause_category'],
            'goal_amount' => $data['goal_amount'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'created_by' => $user->id,
            'status' => Campaign::STATUS_PENDING,
        ];

        if (isset($data['image'])) {
            $campaignData['image'] = $data['image'];
        }

        return Campaign::create($campaignData);
    }

    /**
     * Update an existing campaign.
     */
    public function updateCampaign(Campaign $campaign, array $data): Campaign
    {
        $updateData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'cause_category' => $data['cause_category'],
            'target_amount' => $data['target_amount'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];

        if (isset($data['featured_image'])) {
            $updateData['featured_image'] = $data['featured_image'];
        }

        // If campaign is approved and being edited, set back to pending
        if ($campaign->status === Campaign::STATUS_ACTIVE) {
            $updateData['status'] = Campaign::STATUS_PENDING;
            $updateData['approved_by'] = null;
            $updateData['approved_at'] = null;
        }

        $campaign->update($updateData);
        
        return $campaign->fresh();
    }

    /**
     * Approve a campaign.
     */
    public function approveCampaign(Campaign $campaign, User $approver): Campaign
    {
        $campaign->update([
            'status' => Campaign::STATUS_ACTIVE,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $campaign->fresh();
    }

    /**
     * Toggle featured status of a campaign.
     */
    public function toggleFeatured(Campaign $campaign): Campaign
    {
        $campaign->update([
            'is_featured' => !$campaign->is_featured,
        ]);

        return $campaign->fresh();
    }

    /**
     * Get featured campaigns.
     */
    public function getFeaturedCampaigns(int $limit = 6): Collection
    {
        return Campaign::with(['creator', 'donations'])
            ->where('is_featured', true)
            ->where('status', Campaign::STATUS_ACTIVE)
            ->limit($limit)
            ->get();
    }

    /**
     * Get campaigns by status.
     */
    public function getCampaignsByStatus(string $status): Collection
    {
        return Campaign::with(['creator'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Delete a campaign.
     */
    public function deleteCampaign(Campaign $campaign): bool
    {
        return $campaign->delete();
    }
}