<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        // Calculate profile completion score
        $fields = [
            $user->name,
            $user->email,
            $user->phone,
            $profile?->college_name,
            $profile?->preferred_city,
            $profile?->budget_max,
            $profile?->preferred_room_type,
            $profile?->gender,
        ];
        $filled = count(array_filter($fields));
        $profileCompletion = round(($filled / count($fields)) * 100);

        $savedPropertiesCount = $user->savedProperties()->count();
        $activeInquiriesCount = $user->inquiries()->count();
        $conversationsCount = $user->inquiries()->whereIn('status', ['new', 'contacted', 'visit_scheduled'])->count();

        return view('student.dashboard', compact(
            'user',
            'profile',
            'profileCompletion',
            'savedPropertiesCount',
            'activeInquiriesCount',
            'conversationsCount'
        ));
    }
}
