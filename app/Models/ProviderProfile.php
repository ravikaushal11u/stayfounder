<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'owner_name',
        'phone',
        'whatsapp_number',
        'city',
        'address',
        'id_proof_type',
        'id_proof_path',
        'is_verified',
        'verified_at',
        'verification_notes',
        'subscription_tier',
        'subscription_expires_at',
        'response_rate',
        'avg_response_time',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'response_rate' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPremium(): bool
    {
        return in_array($this->subscription_tier, ['premium', 'premium_plus']);
    }
}
