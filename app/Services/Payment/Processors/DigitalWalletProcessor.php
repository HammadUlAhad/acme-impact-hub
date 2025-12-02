<?php

namespace App\Services\Payment\Processors;

use App\Models\Donation;

class DigitalWalletProcessor extends AbstractPaymentProcessor
{
    /**
     * Process digital wallet payment.
     */
    public function process(Donation $donation): void
    {
        // Simulate digital wallet processing - replace with actual provider integration
        // Example: PayPal, Apple Pay, Google Pay, etc.
        
        try {
            $donation->update([
                'payment_status' => Donation::STATUS_COMPLETED,
                'processed_at' => now(),
                'payment_reference' => $this->generateReference($donation, 'DW'),
            ]);

            $this->updateCampaignAmount($donation);
        } catch (\Exception $e) {
            $donation->update([
                'payment_status' => Donation::STATUS_FAILED,
            ]);
            
            throw $e;
        }
    }

    /**
     * Refund digital wallet payment.
     */
    public function refund(Donation $donation): void
    {
        // Implement digital wallet refund logic
        // This would typically involve calling the wallet provider's refund API
    }

    /**
     * Check if digital wallet is supported.
     */
    public function supports(string $paymentMethod): bool
    {
        return $paymentMethod === Donation::METHOD_DIGITAL_WALLET;
    }
}