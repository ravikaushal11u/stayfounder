<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'property_type',
        'gender_preference',
        'description',
        'address',
        'locality',
        'city',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'monthly_rent_min',
        'monthly_rent_max',
        'security_deposit',
        'notice_period_days',
        'is_verified',
        'is_featured',
        'status',
        'food_included',
        'food_details',
        'gate_closing_time',
        'rules',
        'image_360',
        'nearby_colleges',
        'nearby_transport',
        'total_beds',
        'available_beds',
        'rating',
        'review_count',
        'views_count',
        'inquiries_count',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'monthly_rent_min' => 'integer',
            'monthly_rent_max' => 'integer',
            'security_deposit' => 'integer',
            'notice_period_days' => 'integer',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'food_included' => 'boolean',
            'rules' => 'array',
            'nearby_colleges' => 'array',
            'nearby_transport' => 'array',
            'total_beds' => 'integer',
            'available_beds' => 'integer',
            'rating' => 'float',
            'review_count' => 'integer',
            'views_count' => 'integer',
            'inquiries_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(PropertyRoom::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_properties')->withTimestamps();
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function safetyReports(): HasMany
    {
        return $this->hasMany(SafetyReport::class)->latest();
    }

    public function recalculateRating(): void
    {
        $approved = $this->reviews()->where('is_approved', true);
        $count = $approved->count();
        $avg = $count > 0 ? (float) $approved->avg('rating') : 0.0;

        $this->update([
            'rating' => round($avg, 1),
            'review_count' => $count,
        ]);
    }

    public function isSavedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->savedByUsers()->where('users.id', $user->id)->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeWithDistance(Builder $query, float $lat, float $lng): Builder
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->ensureSqliteHaversine();

            return $query->select('*')
                ->selectRaw("haversine_km(?, ?, latitude, longitude) AS distance_km", [$lat, $lng]);
        }

        $haversine = "(6371 * acos(least(1.0, greatest(-1.0, cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))))";

        return $query->select('*')
            ->selectRaw("{$haversine} AS distance_km", [$lat, $lng, $lat]);
    }

    public function scopeWithinDistance(Builder $query, float $lat, float $lng, float $maxDistanceKm): Builder
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->ensureSqliteHaversine();

            return $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereRaw("CAST(haversine_km(?, ?, latitude, longitude) AS REAL) <= ?", [$lat, $lng, (float) $maxDistanceKm]);
        }

        $haversine = "(6371 * acos(least(1.0, greatest(-1.0, cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))))";

        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereRaw("{$haversine} <= ?", [$lat, $lng, $lat, (float) $maxDistanceKm]);
    }

    protected function ensureSqliteHaversine(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $pdo = DB::connection()->getPdo();
            if (method_exists($pdo, 'sqliteCreateFunction')) {
                $pdo->sqliteCreateFunction('haversine_km', function ($lat1, $lng1, $lat2, $lng2) {
                    if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
                        return null;
                    }
                    $dLat = deg2rad((float)$lat2 - (float)$lat1);
                    $dLng = deg2rad((float)$lng2 - (float)$lng1);
                    $a = sin($dLat / 2) * sin($dLat / 2) +
                         cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) *
                         sin($dLng / 2) * sin($dLng / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(max(0.0, 1.0 - $a)));
                    return 6371 * $c;
                }, 4);
            }
        }
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // 1. Text Search (city, locality, title, colleges)
        if (! empty($filters['query'])) {
            $term = '%' . trim($filters['query']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('locality', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('pincode', 'like', $term)
                    ->orWhere('nearby_colleges', 'like', $term);
            });
        }

        // 2. City
        if (! empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        // 3. Accommodation Type
        if (! empty($filters['type'])) {
            $query->where('property_type', $filters['type']);
        }

        // 4. Gender Preference
        if (! empty($filters['gender']) && $filters['gender'] !== 'any') {
            $query->where(function ($q) use ($filters) {
                $q->where('gender_preference', $filters['gender'])
                    ->orWhere('gender_preference', 'any');
            });
        }

        // 5. Budget Max
        if (! empty($filters['budget'])) {
            $query->where('monthly_rent_min', '<=', (int) $filters['budget']);
        }

        // 6. Food Included
        if (isset($filters['food']) && $filters['food'] !== '') {
            $query->where('food_included', (bool) $filters['food']);
        }

        // 7. Room Type (Single, Double, Triple, 4+)
        if (! empty($filters['room_type'])) {
            $roomType = $filters['room_type'];
            $query->whereHas('rooms', function ($q) use ($roomType) {
                $q->where('room_type', $roomType);
            });
        }

        // 8. Amenities
        if (! empty($filters['amenities']) && is_array($filters['amenities'])) {
            foreach ($filters['amenities'] as $amenitySlug) {
                $query->whereHas('amenities', function ($q) use ($amenitySlug) {
                    $q->where('slug', $amenitySlug);
                });
            }
        }

        // 9. Location Coordinates + Radius
        if (! empty($filters['lat']) && ! empty($filters['lng'])) {
            $lat = (float) $filters['lat'];
            $lng = (float) $filters['lng'];
            $maxDist = ! empty($filters['distance']) ? (float) $filters['distance'] : 10.0;

            $query->withinDistance($lat, $lng, $maxDist)
                ->withDistance($lat, $lng)
                ->orderBy('distance_km', 'asc');
        } else {
            // Default Sort: Featured listings first, then rating, then latest
            $query->orderByDesc('is_featured')
                ->orderByDesc('rating')
                ->latest();
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */
    public function getPrimaryImageUrlAttribute(): string
    {
        $img = $this->images->first();
        if ($img) {
            return $img->url;
        }

        return 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=800&q=80';
    }

    public function getImage360UrlAttribute(): ?string
    {
        if (! $this->image_360) {
            return null;
        }

        return asset('storage/' . $this->image_360);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->property_type) {
            'pg' => 'Paying Guest (PG)',
            'hostel' => 'Student Hostel',
            'room' => 'Private Room',
            'flat' => 'Student Flat',
            'lodge' => 'Lodge / Exam Stay',
            default => ucfirst($this->property_type),
        };
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender_preference) {
            'male' => 'Boys Only',
            'female' => 'Girls Only',
            default => 'Co-ed / Any',
        };
    }

    public function getRentDisplayAttribute(): string
    {
        if ($this->monthly_rent_max && $this->monthly_rent_max > $this->monthly_rent_min) {
            return '₹' . number_format($this->monthly_rent_min) . ' - ₹' . number_format($this->monthly_rent_max) . '/mo';
        }

        return '₹' . number_format($this->monthly_rent_min) . '/mo';
    }

    public function getNearestCollegeTextAttribute(): ?string
    {
        if (! empty($this->nearby_colleges) && is_array($this->nearby_colleges) && count($this->nearby_colleges) > 0) {
            $first = $this->nearby_colleges[0];
            $distance = $first['distance_km'] ?? null;
            $name = $first['name'] ?? null;
            if ($name && $distance) {
                return "{$distance} km from {$name}";
            }
            return $name;
        }

        return null;
    }
}
