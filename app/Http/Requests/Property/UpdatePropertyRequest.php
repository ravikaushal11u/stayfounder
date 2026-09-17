<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $property = $this->route('property');
        return $this->user()->can('update', $property);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'property_type' => ['required', 'string', 'in:pg,hostel,room,flat,lodge'],
            'gender_preference' => ['required', 'string', 'in:male,female,any'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:500'],
            'locality' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'monthly_rent_min' => ['required', 'numeric', 'min:500'],
            'monthly_rent_max' => ['nullable', 'numeric', 'gte:monthly_rent_min'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'notice_period_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'total_beds' => ['nullable', 'integer', 'min:1'],
            'available_beds' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,paused,draft'],
            'food_included' => ['nullable', 'boolean'],
            'food_details' => ['nullable', 'string', 'max:1000'],
            'gate_closing_time' => ['nullable', 'string', 'max:50'],
            'rules' => ['nullable', 'array'],
            'rules.*' => ['nullable', 'string', 'max:255'],
            'nearby_colleges' => ['nullable', 'array'],
            'nearby_colleges.*.name' => ['nullable', 'string', 'max:255'],
            'nearby_colleges.*.distance_km' => ['nullable', 'numeric', 'min:0'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['exists:amenities,id'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'image_360' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:15360'], // max 15MB for panorama
            'rooms' => ['nullable', 'array'],
            'rooms.*.id' => ['nullable', 'integer'],
            'rooms.*.room_type' => ['required', 'string', 'in:single,double,triple,four_plus'],
            'rooms.*.title' => ['required', 'string', 'max:150'],
            'rooms.*.monthly_rent' => ['required', 'numeric', 'min:500'],
            'rooms.*.security_deposit' => ['nullable', 'numeric', 'min:0'],
            'rooms.*.total_capacity' => ['nullable', 'integer', 'min:1'],
            'rooms.*.available_capacity' => ['nullable', 'integer', 'min:0'],
            'rooms.*.has_attached_bathroom' => ['nullable', 'boolean'],
            'rooms.*.has_ac' => ['nullable', 'boolean'],
            'rooms.*.has_balcony' => ['nullable', 'boolean'],
        ];
    }
}
