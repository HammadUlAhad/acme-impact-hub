<?php

namespace App\Services\Payment;

use App\Models\Donation;

interface PaymentProcessorInterface
{
    /**
     * Process the payment for a donation.
     */
    public function process(Donation $donation): void;

    /**
     * Refund the payment for a donation.
     */
    public function refund(Donation $donation): void;

    /**
     * Get the payment status.
     */
    public function getStatus(string $reference): string;

    /**
     * Check if the payment method is supported.
     */
    public function supports(string $paymentMethod): bool;
}