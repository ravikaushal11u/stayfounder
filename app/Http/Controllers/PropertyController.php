<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Search and browse student accommodation listings.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only([
            'query',
            'city',
            'type',
            'gender',
            'budget',
            'room_type',
            'food',
            'amenities',
            'distance',
            'lat',
            'lng',
            'sort',
        ]);

        $query = Property::active()->with([
            'images',
            'amenities',
            'rooms',
            'user.providerProfile',
        ]);

        // Apply filters
        $query->filter($filters);

        // Smart proximity fallback: if a locality query returns 0 text matches, resolve coordinates and search nearby
        $proximityNote = null;
        if (! empty($filters['query']) && (clone $query)->count() === 0) {
            $coords = \App\Services\Geocoding\LocationService::resolveCoordinates($filters['query']);
            if ($coords) {
                $proximityQuery = Property::active()->with([
                    'images',
                    'amenities',
                    'rooms',
                    'user.providerProfile',
                ]);
                $proximityFilters = $filters;
                $proximityFilters['lat'] = $coords[0];
                $proximityFilters['lng'] = $coords[1];
                $proximityFilters['distance'] = 15;
                unset($proximityFilters['query']);
                $proximityQuery->filter($proximityFilters);
                if ($proximityQuery->count() > 0) {
                    $query = $proximityQuery;
                    $proximityNote = "Showing verified stays nearby within 15 km of \"" . e($filters['query']) . "\"";
                }
            }
        }

        // Sorting options
        $sort = $request->input('sort');
        if ($sort === 'rent_asc') {
            $query->orderBy('monthly_rent_min', 'asc');
        } elseif ($sort === 'rent_desc') {
            $query->orderBy('monthly_rent_min', 'desc');
        } elseif ($sort === 'rating') {
            $query->orderBy('rating', 'desc');
        }

        // Server-side pagination (9 per page)
        $properties = $query->paginate(9)->withQueryString();

        // Data for sidebar filters
        $popularAmenities = Amenity::where('is_popular', true)->get();
        $allCities = Property::active()->distinct()->pluck('city')->sort()->values();

        // Data for Interactive Map
        $mapProperties = $properties->getCollection()->map(function ($p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'url' => route('properties.show', $p->slug),
                'image' => $p->primary_image_url,
                'lat' => (float) $p->latitude,
                'lng' => (float) $p->longitude,
                'price' => '₹' . number_format($p->monthly_rent_min),
                'rent_display' => $p->rent_display,
                'type' => $p->type_label,
                'gender' => $p->gender_label,
                'locality' => $p->locality,
                'city' => $p->city,
                'available_beds' => $p->available_beds,
            ];
        })->filter(fn($p) => !empty($p['lat']) && !empty($p['lng']))->values();

        $topColleges = [
            ['name' => 'Symbiosis International', 'query' => 'Symbiosis', 'city' => 'Pune'],
            ['name' => 'COEP Technological University', 'query' => 'COEP', 'city' => 'Pune'],
            ['name' => 'Delhi University', 'query' => 'Delhi University', 'city' => 'Delhi'],
            ['name' => 'IIT Delhi', 'query' => 'IIT Delhi', 'city' => 'Delhi'],
            ['name' => 'Christ University', 'query' => 'Christ', 'city' => 'Bengaluru'],
            ['name' => 'Kota Coaching Hub', 'query' => 'Kota', 'city' => 'Kota'],
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'properties' => $mapProperties,
                'total' => $properties->total(),
            ]);
        }

        return view('properties.index', compact(
            'properties',
            'filters',
            'popularAmenities',
            'allCities',
            'mapProperties',
            'topColleges',
            'proximityNote'
        ));
    }

    /**
     * Display a specific student property with details, room options, and contact.
     */
    public function show(string $slug): View
    {
        $property = Property::where('slug', $slug)
            ->active()
            ->with([
                'images',
                'rooms',
                'amenities',
                'user.providerProfile',
                'reviews.user.studentProfile',
            ])
            ->firstOrFail();

        // Increment views count safely
        $property->increment('views_count');

        // Similar properties in same city / type
        $similarProperties = Property::active()
            ->where('id', '!=', $property->id)
            ->where('city', $property->city)
            ->with(['images', 'amenities', 'user.providerProfile'])
            ->take(3)
            ->get();

        return view('properties.show', compact('property', 'similarProperties'));
    }
}
