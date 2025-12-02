<?php

namespace App\Listeners;

use App\Events\CampaignApproved;
use App\Mail\CampaignApprovedMail;
use Illuminate\Support\Facades\Mail;

class SendCampaignApprovedNotification
{
    public function handle(CampaignApproved $event): void
    {
        // Send email notification to campaign creator
        Mail::to($event->campaign->creator->email)
            ->queue(new CampaignApprovedMail($event->campaign));

        // Log the approval
        \Log::info('Campaign approved', [
            'campaign_id' => $event->campaign->id,
            'campaign_title' => $event->campaign->title,
            'approved_by' => $event->approvedBy->name,
            'approved_at' => now(),
        ]);
    }
}