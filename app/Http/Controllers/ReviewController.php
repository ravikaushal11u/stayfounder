<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new student review for a property.
     */
    public function store(StoreReviewRequest $request, Property $property): RedirectResponse
    {
        $user = $request->user();

        if ($property->user_id === $user->id) {
            return back()->with('warning', 'You cannot review your own accommodation listing.');
        }

        $validated = $request->validated();

        Review::updateOrCreate(
            [
                'property_id' => $property->id,
                'user_id' => $user->id,
            ],
            array_merge($validated, ['is_approved' => true])
        );

        // Recalculate property aggregate rating and review count
        $property->recalculateRating();

        return back()->with('success', 'Thank you! Your verified student review has been published.');
    }

    /**
     * Provider replies publicly to a student review.
     */
    public function reply(Request $request, Review $review): RedirectResponse
    {
        $user = $request->user();

        if ($review->property->user_id !== $user->id) {
            abort(403, 'Only the accommodation owner can reply to this review.');
        }

        $validated = $request->validate([
            'provider_reply' => ['required', 'string', 'max:1000'],
        ]);

        $review->update([
            'provider_reply' => trim($validated['provider_reply']),
            'provider_replied_at' => now(),
        ]);

        return back()->with('success', 'Your official response has been published on the review.');
    }
}
