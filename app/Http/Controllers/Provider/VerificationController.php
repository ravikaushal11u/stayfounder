<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Verification\StoreVerificationRequest;
use App\Models\VerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Show the provider verification request console.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $profile = $user->providerProfile;

        $requests = VerificationRequest::where('user_id', $user->id)
            ->latest()
            ->get();

        $properties = $user->properties()->select('id', 'title')->get();

        return view('provider.verification.create', compact('user', 'profile', 'requests', 'properties'));
    }

    /**
     * Submit a new verification request with documents.
     */
    public function store(StoreVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $path = $request->file('document')->store("verifications/{$user->id}", 'public');

        VerificationRequest::create([
            'user_id' => $user->id,
            'property_id' => $validated['property_id'] ?? null,
            'document_type' => $validated['document_type'],
            'document_path' => $path,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Verification documents uploaded successfully! Our safety audit team will inspect your credentials within 24-48 hours.');
    }
}
