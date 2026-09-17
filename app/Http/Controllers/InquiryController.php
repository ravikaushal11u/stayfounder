<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inquiry\StoreInquiryRequest;
use App\Models\Property;
use App\Models\PropertyInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InquiryController extends Controller
{
    /**
     * Submit a student accommodation inquiry to a provider.
     */
    public function store(StoreInquiryRequest $request, Property $property): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $inquiry = PropertyInquiry::create([
            'user_id' => Auth::id(),
            'property_id' => $property->id,
            'provider_id' => $property->user_id,
            'student_name' => $validated['student_name'],
            'student_phone' => $validated['student_phone'],
            'student_email' => $validated['student_email'] ?? (Auth::user()?->email),
            'target_move_in_date' => $validated['target_move_in_date'],
            'preferred_room_type' => $validated['preferred_room_type'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        // Increment inquiries counter on property
        $property->increment('inquiries_count');

        $message = 'Inquiry sent successfully! The property provider will contact you shortly.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'inquiry_id' => $inquiry->id,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Display student's sent inquiries.
     */
    public function studentIndex(Request $request): View
    {
        $user = $request->user();

        $inquiries = PropertyInquiry::where('user_id', $user->id)
            ->with(['property.images', 'provider.providerProfile'])
            ->latest()
            ->paginate(10);

        return view('student.inquiries', compact('inquiries'));
    }
}
