<?php

namespace App\Services\Payment\Processors;

use App\Models\Donation;

class PayrollDeductionProcessor extends AbstractPaymentProcessor
{
    /**
     * Process payroll deduction payment.
     */
    public function process(Donation $donation): void
    {
        // For payroll deduction, we auto-approve since it's internal
        $donation->update([
            'payment_status' => Donation::STATUS_COMPLETED,
            'processed_at' => now(),
            'payment_reference' => $this->generateReference($donation, 'PAYROLL'),
        ]);

        $this->updateCampaignAmount($donation);
    }

    /**
     * Refund payroll deduction payment.
     */
    public function refund(Donation $donation): void
    {
        // Implement payroll deduction refund logic
        // This would typically involve notifying HR systems
    }

    /**
     * Check if payroll deduction is supported.
     */
    public function supports(string $paymentMethod): bool
    {
        return $paymentMethod === Donation::METHOD_PAYROLL_DEDUCTION;
    }
}