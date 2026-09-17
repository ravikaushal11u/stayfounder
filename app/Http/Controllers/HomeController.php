<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the student accommodation marketplace homepage.
     */
    public function index(Request $request): View
    {
        // Load featured verified listings for homepage display
        $featuredProperties = Property::active()
            ->with(['images', 'amenities', 'user.providerProfile'])
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->take(6)
            ->get();

        $popularAmenities = Amenity::where('is_popular', true)->take(8)->get();

        return view('home', compact('featuredProperties', 'popularAmenities'));
    }
}
