<?php

namespace App\Services\Payment\Processors;

use App\Models\Donation;

class BankTransferProcessor extends AbstractPaymentProcessor
{
    /**
     * Process bank transfer payment.
     */
    public function process(Donation $donation): void
    {
        // Bank transfer requires manual verification
        $donation->update([
            'payment_status' => Donation::STATUS_PROCESSING,
            'payment_reference' => $this->generateReference($donation, 'BANK'),
        ]);
    }

    /**
     * Refund bank transfer payment.
     */
    public function refund(Donation $donation): void
    {
        // Implement bank transfer refund logic
        // This would typically involve initiating a bank transfer back
    }

    /**
     * Check if bank transfer is supported.
     */
    public function supports(string $paymentMethod): bool
    {
        return $paymentMethod === Donation::METHOD_BANK_TRANSFER;
    }
}