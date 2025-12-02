<?php

namespace App\Providers;

use App\Events\CampaignApproved;
use App\Events\CampaignCreated;
use App\Events\DonationCreated;
use App\Events\DonationProcessed;
use App\Listeners\SendCampaignApprovedNotification;
use App\Listeners\SendDonationReceipt;
use App\Listeners\UpdateCampaignAmount;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listeners
        Event::listen(CampaignApproved::class, SendCampaignApprovedNotification::class);
        Event::listen(DonationCreated::class, SendDonationReceipt::class);
        Event::listen(DonationProcessed::class, UpdateCampaignAmount::class);
    }
}
