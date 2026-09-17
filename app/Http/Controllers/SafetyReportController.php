<?php

namespace App\Http\Controllers;

use App\Http\Requests\Safety\StoreSafetyReportRequest;
use App\Models\Property;
use App\Models\SafetyReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SafetyReportController extends Controller
{
    /**
     * Submit a safety or scam report on a property.
     */
    public function store(StoreSafetyReportRequest $request, Property $property): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        SafetyReport::create([
            'property_id' => $property->id,
            'user_id' => Auth::id(),
            'reporter_name' => $validated['reporter_name'] ?? (Auth::user()?->name),
            'reporter_phone' => $validated['reporter_phone'] ?? (Auth::user()?->phone),
            'reason' => $validated['reason'],
            'description' => trim($validated['description']),
            'status' => 'pending',
        ]);

        $message = 'Thank you for reporting this issue. Our trust & safety team will review this listing immediately.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
