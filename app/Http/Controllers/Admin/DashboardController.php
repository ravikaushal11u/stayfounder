<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request): View
    {
        $totalUsers = User::count();
        $totalStudents = User::where('role', UserRole::STUDENT)->count();
        $totalProviders = User::where('role', UserRole::PROVIDER)->count();
        $verifiedProviders = ProviderProfile::where('is_verified', true)->count();
        $pendingVerifications = ProviderProfile::where('is_verified', false)->count();

        $recentUsers = User::latest()->take(8)->get();

        $metrics = [
            'total_users' => $totalUsers,
            'total_students' => $totalStudents,
            'total_providers' => $totalProviders,
            'verified_providers' => $verifiedProviders,
            'pending_verifications' => $pendingVerifications,
            'active_listings' => 0,
            'reported_listings' => 0,
            'monthly_revenue' => 0,
        ];

        return view('admin.dashboard', compact('metrics', 'recentUsers'));
    }
}
