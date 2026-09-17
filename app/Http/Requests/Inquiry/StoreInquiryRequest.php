<?php

namespace App\Http\Requests\Inquiry;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Anyone can send an inquiry (guest or authenticated student)
    }

    public function rules(): array
    {
        return [
            'student_name' => ['required', 'string', 'max:150'],
            'student_phone' => ['required', 'string', 'max:25'],
            'student_email' => ['nullable', 'email', 'max:150'],
            'target_move_in_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_room_type' => ['nullable', 'string', 'in:single,double,triple,four_plus,any'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_name.required' => 'Please enter your name so the provider knows who to contact.',
            'student_phone.required' => 'A valid phone or WhatsApp number is required for the provider to reply.',
            'target_move_in_date.required' => 'Please select an estimated move-in date.',
            'target_move_in_date.after_or_equal' => 'Move-in date must be today or a future date.',
        ];
    }
}
