<?php

namespace App\Http\Requests\Donation;

use App\Models\Donation;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $campaign = $this->route('campaign');
        return $campaign && $campaign->isAcceptingDonations();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1', 'max:10000'],
            'payment_method' => ['required', 'in:' . implode(',', array_keys(Donation::getPaymentMethods()))],
            'is_anonymous' => ['boolean'],
            'donor_message' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Donation amount is required.',
            'amount.min' => 'Minimum donation amount is $1.',
            'amount.max' => 'Maximum donation amount is $10,000.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'donor_message.max' => 'Message cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'payment_method' => 'payment method',
            'donor_message' => 'message',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => (float) $this->amount,
            'is_anonymous' => $this->boolean('is_anonymous'),
        ]);
    }
}