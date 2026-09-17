<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'cleanliness_rating' => ['nullable', 'integer', 'between:1,5'],
            'food_rating' => ['nullable', 'integer', 'between:1,5'],
            'wifi_rating' => ['nullable', 'integer', 'between:1,5'],
            'safety_rating' => ['nullable', 'integer', 'between:1,5'],
            'behavior_rating' => ['nullable', 'integer', 'between:1,5'],
            'review' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Please select an overall star rating from 1 to 5.',
            'review.required' => 'Please share your honest feedback and experience.',
            'review.min' => 'Your review must be at least 10 characters long.',
        ];
    }
}
