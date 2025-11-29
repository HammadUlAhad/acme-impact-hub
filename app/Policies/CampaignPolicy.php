<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    /**
     * Determine whether the user can view any campaigns.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view campaigns
    }

    /**
     * Determine whether the user can view the campaign.
     */
    public function view(User $user, Campaign $campaign): bool
    {
        // Users can view active campaigns or their own campaigns in any status
        return $campaign->status === Campaign::STATUS_ACTIVE 
            || $campaign->created_by === $user->id
            || $user->hasRole(['admin', 'campaign_manager']);
    }

    /**
     * Determine whether the user can create campaigns.
     */
    public function create(User $user): bool
    {
        // Allow all authenticated users to create campaigns
        // In a real scenario, you might want to restrict this to specific roles
        return true;
    }

    /**
     * Determine whether the user can update the campaign.
     */
    public function update(User $user, Campaign $campaign): bool
    {
        // Users can edit their own campaigns in draft/pending status
        // Admins can edit any campaign
        return ($campaign->created_by === $user->id && 
                in_array($campaign->status, [Campaign::STATUS_DRAFT, Campaign::STATUS_PENDING]))
            || $user->hasPermissionTo('edit-all-campaigns');
    }

    /**
     * Determine whether the user can delete the campaign.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        // Users can delete their own campaigns in draft status
        // Admins can delete any campaign
        return ($campaign->created_by === $user->id && $campaign->status === Campaign::STATUS_DRAFT)
            || $user->hasPermissionTo('delete-campaigns');
    }

    /**
     * Determine whether the user can approve the campaign.
     */
    public function approve(User $user, Campaign $campaign): bool
    {
        return $campaign->status === Campaign::STATUS_PENDING 
            && $user->hasPermissionTo('approve-campaigns');
    }

    /**
     * Determine whether the user can feature the campaign.
     */
    public function feature(User $user, Campaign $campaign): bool
    {
        return $user->hasPermissionTo('feature-campaigns');
    }
}
