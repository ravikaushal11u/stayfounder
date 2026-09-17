<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'rating',
        'cleanliness_rating',
        'food_rating',
        'wifi_rating',
        'safety_rating',
        'behavior_rating',
        'review',
        'provider_reply',
        'provider_replied_at',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'cleanliness_rating' => 'integer',
            'food_rating' => 'integer',
            'wifi_rating' => 'integer',
            'safety_rating' => 'integer',
            'behavior_rating' => 'integer',
            'is_approved' => 'boolean',
            'provider_replied_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }
}
