<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the provider dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->providerProfile;

        $properties = $user->properties()->with(['images', 'rooms'])->latest()->get();

        $stats = [
            'total_listings' => $properties->count(),
            'active_listings' => $properties->where('status', 'active')->count(),
            'total_views' => (int) $properties->sum('views_count'),
            'total_inquiries' => (int) $properties->sum('inquiries_count'),
            'total_calls' => (int) ($properties->sum('inquiries_count') * 2), // direct calls estimate
            'total_chats' => (int) $properties->sum('inquiries_count'),
            'saved_count' => (int) ($properties->sum('views_count') * 0.15),
        ];

        $recentProperties = $properties->take(5);

        return view('provider.dashboard', compact('user', 'profile', 'stats', 'recentProperties'));
    }
}
