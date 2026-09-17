<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SavedProperty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * Display student's saved favorite stays.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $savedProperties = $user->favoriteProperties()
            ->with(['images', 'rooms', 'amenities', 'user.providerProfile'])
            ->latest('saved_properties.created_at')
            ->paginate(12);

        return view('student.saved-stays', compact('savedProperties'));
    }

    /**
     * Toggle saved / favorite status for a property.
     */
    public function toggle(Request $request, Property $property): JsonResponse|RedirectResponse
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Please sign in to save accommodations.',
                    'redirect' => route('login'),
                ], 401);
            }

            return redirect()->route('login')
                ->with('warning', 'Please sign in to save properties to your account.');
        }

        $user = Auth::user();
        $existing = SavedProperty::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isSaved = false;
            $message = 'Removed from your saved stays.';
        } else {
            SavedProperty::create([
                'user_id' => $user->id,
                'property_id' => $property->id,
            ]);
            $isSaved = true;
            $message = 'Added to your saved stays!';
        }

        $totalSaved = $user->savedProperties()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_saved' => $isSaved,
                'total_saved' => $totalSaved,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
