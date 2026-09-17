<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'document_type',
        'document_path',
        'notes',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            'aadhaar' => 'Government ID / Aadhaar',
            'electricity_bill' => 'Commercial Electricity Bill',
            'trade_license' => 'Municipal Trade License',
            'property_tax' => 'Property Tax Receipt',
            default => ucfirst(str_replace('_', ' ', $this->document_type)),
        };
    }
}
