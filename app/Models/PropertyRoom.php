<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_type',
        'title',
        'monthly_rent',
        'security_deposit',
        'total_capacity',
        'available_capacity',
        'has_attached_bathroom',
        'has_ac',
        'has_balcony',
    ];

    protected function casts(): array
    {
        return [
            'monthly_rent' => 'integer',
            'security_deposit' => 'integer',
            'total_capacity' => 'integer',
            'available_capacity' => 'integer',
            'has_attached_bathroom' => 'boolean',
            'has_ac' => 'boolean',
            'has_balcony' => 'boolean',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getRoomTypeLabelAttribute(): string
    {
        return match ($this->room_type) {
            'single' => 'Single Occupancy (Private)',
            'double' => 'Double Sharing',
            'triple' => 'Triple Sharing',
            'four_plus' => '4+ Bed Sharing',
            default => ucfirst($this->room_type),
        };
    }
}
