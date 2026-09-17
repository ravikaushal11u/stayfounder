<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'phone_verified_at',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function providerProfile(): HasOne
    {
        return $this->hasOne(ProviderProfile::class);
    }

    public function properties(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function savedProperties(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SavedProperty::class);
    }

    public function favoriteProperties(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'saved_properties')->withTimestamps();
    }

    public function inquiries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    public function receivedInquiries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PropertyInquiry::class, 'provider_id');
    }

    public function hasSaved(Property $property): bool
    {
        return $this->savedProperties()->where('property_id', $property->id)->exists();
    }

    public function conversations()
    {
        return Conversation::where(function ($q) {
            $q->where('student_id', $this->id)
              ->orWhere('provider_id', $this->id);
        });
    }

    public function unreadMessagesCount(): int
    {
        return Message::whereHas('conversation', function ($q) {
            $q->where('student_id', $this->id)->orWhere('provider_id', $this->id);
        })
        ->where('sender_id', '!=', $this->id)
        ->whereNull('read_at')
        ->count();
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::STUDENT;
    }

    public function isProvider(): bool
    {
        return $this->role === UserRole::PROVIDER;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function getDashboardUrl(): string
    {
        return match ($this->role) {
            UserRole::STUDENT => route('student.dashboard'),
            UserRole::PROVIDER => route('provider.dashboard'),
            UserRole::ADMIN => route('admin.dashboard'),
            default => route('home'),
        };
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest();
    }

    public function currentSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->first();
    }

    public function activePlan(): ?SubscriptionPlan
    {
        return $this->currentSubscription()?->plan;
    }
}
