<?php

namespace App\Http\Requests\Verification;

use Illuminate\Foundation\Http\FormRequest;

class StoreVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'string', 'in:aadhaar,electricity_bill,trade_license,property_tax'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type.required' => 'Please select the type of verification document you are uploading.',
            'document.required' => 'Please upload an official document copy (PDF, JPG, PNG under 5MB).',
        ];
    }
}
