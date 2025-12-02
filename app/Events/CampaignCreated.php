<?php

namespace App\Events;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Campaign $campaign
    ) {}
}