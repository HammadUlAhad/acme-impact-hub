<?php

namespace App\Listeners;

use App\Events\DonationCreated;
use App\Mail\DonationReceiptMail;
use Illuminate\Support\Facades\Mail;

class SendDonationReceipt
{
    public function handle(DonationCreated $event): void
    {
        $donation = $event->donation;

        // Send receipt email to donor
        Mail::to($donation->user->email)
            ->queue(new DonationReceiptMail($donation));

        // Log the donation
        \Log::info('Donation created', [
            'donation_id' => $donation->id,
            'campaign_id' => $donation->campaign_id,
            'user_id' => $donation->user_id,
            'amount' => $donation->amount,
            'payment_method' => $donation->payment_method,
            'created_at' => $donation->created_at,
        ]);
    }
}