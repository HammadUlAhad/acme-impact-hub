<?php

namespace App\Providers;

use App\Repositories\CampaignRepository;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Repositories\Contracts\DonationRepositoryInterface;
use App\Repositories\DonationRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            CampaignRepositoryInterface::class,
            CampaignRepository::class
        );

        $this->app->bind(
            DonationRepositoryInterface::class,
            DonationRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}