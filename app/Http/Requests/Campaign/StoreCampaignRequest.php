<?php

namespace App\Http\Requests\Campaign;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Campaign::class);
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
            'goal_amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
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
            'goal_amount.min' => 'Goal amount must be at least $1.',
            'goal_amount.max' => 'Goal amount cannot exceed $1,000,000.',
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
            'goal_amount' => 'goal amount',
            'start_date' => 'start date',
            'end_date' => 'end date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'goal_amount' => (float) $this->goal_amount,
        ]);
    }
}