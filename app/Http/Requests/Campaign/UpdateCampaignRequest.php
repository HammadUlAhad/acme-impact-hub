<?php

namespace App\Http\Requests\Campaign;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('campaign'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50', 'max:5000'],
            'cause_category' => ['required', 'in:' . implode(',', array_keys(Campaign::getCauseCategories()))],
            'target_amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Campaign title is required.',
            'description.min' => 'Campaign description must be at least 50 characters.',
            'target_amount.min' => 'Target amount must be at least $1.',
            'target_amount.max' => 'Target amount cannot exceed $1,000,000.',
            'end_date.after' => 'End date must be after the start date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cause_category' => 'cause category',
            'target_amount' => 'target amount',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'featured_image' => 'featured image',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'target_amount' => (float) $this->target_amount,
        ]);
    }
}