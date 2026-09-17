<?php

namespace App\Http\Requests\Safety;

use Illuminate\Foundation\Http\FormRequest;

class StoreSafetyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'in:advance_payment_scam,fake_photos,incorrect_pricing,unsafe_environment,already_full,other'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'reporter_name' => ['nullable', 'string', 'max:150'],
            'reporter_phone' => ['nullable', 'string', 'max:25'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Please choose a primary safety reason.',
            'description.required' => 'Please provide specific details to help our moderation team investigate.',
            'description.min' => 'Please write at least 10 characters explaining what occurred.',
        ];
    }
}
