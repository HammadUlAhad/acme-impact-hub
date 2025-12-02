<?php

namespace App\Listeners;

use App\Events\DonationProcessed;

class UpdateCampaignAmount
{
    public function handle(DonationProcessed $event): void
    {
        $donation = $event->donation;
        $campaign = $donation->campaign;

        // Update campaign current amount
        $campaign->increment('current_amount', $donation->amount);

        // Check if campaign has reached its goal
        if ($campaign->current_amount >= $campaign->target_amount && $campaign->status === $campaign::STATUS_ACTIVE) {
            $campaign->update(['status' => $campaign::STATUS_COMPLETED]);
            
            // Log campaign completion
            \Log::info('Campaign completed - goal reached', [
                'campaign_id' => $campaign->id,
                'campaign_title' => $campaign->title,
                'target_amount' => $campaign->target_amount,
                'current_amount' => $campaign->current_amount,
                'completion_date' => now(),
            ]);
        }
    }
}