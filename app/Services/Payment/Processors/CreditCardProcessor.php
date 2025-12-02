<?php

namespace App\Services\Payment\Processors;

use App\Models\Donation;

class CreditCardProcessor extends AbstractPaymentProcessor
{
    /**
     * Process credit card payment.
     */
    public function process(Donation $donation): void
    {
        // Simulate payment processing - replace with actual payment gateway integration
        // Example: Stripe, PayPal, Square, etc.
        
        try {
            // Mock payment processing - in real implementation:
            // 1. Call payment gateway API
            // 2. Handle response
            // 3. Update donation status based on result
            
            $donation->update([
                'payment_status' => Donation::STATUS_COMPLETED,
                'processed_at' => now(),
                'payment_reference' => $this->generateReference($donation, 'CC'),
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
     * Refund credit card payment.
     */
    public function refund(Donation $donation): void
    {
        // Implement credit card refund logic
        // This would typically involve calling the payment gateway's refund API
    }

    /**
     * Check if credit card is supported.
     */
    public function supports(string $paymentMethod): bool
    {
        return $paymentMethod === Donation::METHOD_CREDIT_CARD;
    }
}