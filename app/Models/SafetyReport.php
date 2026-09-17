<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafetyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'reporter_name',
        'reporter_phone',
        'reason',
        'description',
        'status',
        'admin_notes',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'advance_payment_scam' => 'Demanding Advance Money Before Visit',
            'fake_photos' => 'Fake or Misleading Photos',
            'incorrect_pricing' => 'Incorrect Pricing or Hidden Charges',
            'unsafe_environment' => 'Unsafe Environment or Harassment',
            'already_full' => 'Accommodation is Fully Occupied / Unavailable',
            'other' => 'Other Concern',
            default => ucfirst(str_replace('_', ' ', $this->reason)),
        };
    }
}
