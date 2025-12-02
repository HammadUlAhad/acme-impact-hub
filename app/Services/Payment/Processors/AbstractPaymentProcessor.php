<?php

namespace App\Services\Payment\Processors;

use App\Models\Donation;
use App\Services\Payment\PaymentProcessorInterface;

abstract class AbstractPaymentProcessor implements PaymentProcessorInterface
{
    /**
     * Generate a unique payment reference.
     */
    protected function generateReference(Donation $donation, string $prefix): string
    {
        return $prefix . '_' . now()->format('YmdHis') . '_' . $donation->id;
    }

    /**
     * Update campaign amount after successful payment.
     */
    protected function updateCampaignAmount(Donation $donation): void
    {
        $donation->campaign->increment('current_amount', $donation->amount);
    }

    /**
     * Get the payment status.
     */
    public function getStatus(string $reference): string
    {
        // Default implementation - override in specific processors
        return Donation::STATUS_PENDING;
    }
}