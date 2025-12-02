<?php

namespace App\Services\Payment;

use App\Models\Donation;
use App\Services\Payment\Processors\BankTransferProcessor;
use App\Services\Payment\Processors\CreditCardProcessor;
use App\Services\Payment\Processors\DigitalWalletProcessor;
use App\Services\Payment\Processors\PayrollDeductionProcessor;

class PaymentProcessorFactory
{
    /**
     * Create a payment processor for the given payment method.
     */
    public function create(string $paymentMethod): PaymentProcessorInterface
    {
        return match ($paymentMethod) {
            Donation::METHOD_PAYROLL_DEDUCTION => new PayrollDeductionProcessor(),
            Donation::METHOD_BANK_TRANSFER => new BankTransferProcessor(),
            Donation::METHOD_CREDIT_CARD => new CreditCardProcessor(),
            Donation::METHOD_DIGITAL_WALLET => new DigitalWalletProcessor(),
            default => throw new \InvalidArgumentException("Unsupported payment method: {$paymentMethod}")
        };
    }

    /**
     * Get all available processors.
     *
     * @return array<string, PaymentProcessorInterface>
     */
    public function getAllProcessors(): array
    {
        return [
            Donation::METHOD_PAYROLL_DEDUCTION => new PayrollDeductionProcessor(),
            Donation::METHOD_BANK_TRANSFER => new BankTransferProcessor(),
            Donation::METHOD_CREDIT_CARD => new CreditCardProcessor(),
            Donation::METHOD_DIGITAL_WALLET => new DigitalWalletProcessor(),
        ];
    }
}