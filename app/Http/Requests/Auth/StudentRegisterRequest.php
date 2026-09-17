<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StudentRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'college_name' => ['nullable', 'string', 'max:255'],
            'course_or_degree' => ['nullable', 'string', 'max:255'],
            'preferred_city' => ['nullable', 'string', 'max:100'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'gte:budget_min'],
            'preferred_room_type' => ['nullable', 'string', 'in:single,double,triple,four_plus,any'],
        ];
    }

    public function messages(): array
    {
        return [
            'budget_max.gte' => 'The maximum budget must be greater than or equal to the minimum budget.',
        ];
    }
}
