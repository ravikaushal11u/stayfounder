<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'provider_id',
        'student_name',
        'student_phone',
        'student_email',
        'target_move_in_date',
        'preferred_room_type',
        'message',
        'status',
        'provider_notes',
        'contacted_at',
    ];

    protected function casts(): array
    {
        return [
            'target_move_in_date' => 'date',
            'contacted_at' => 'datetime',
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

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function scopeForProvider(Builder $query, int $providerId): Builder
    {
        return $query->where('provider_id', $providerId);
    }

    public function scopeForStudent(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'New Lead',
            'contacted' => 'Contacted',
            'visit_scheduled' => 'Visit Scheduled',
            'converted' => 'Admitted / Converted',
            'closed' => 'Closed',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'new' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'contacted' => 'bg-blue-50 text-blue-700 border-blue-200',
            'visit_scheduled' => 'bg-amber-50 text-amber-800 border-amber-200',
            'converted' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'closed' => 'bg-slate-100 text-slate-600 border-slate-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
