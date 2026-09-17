<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\PropertyInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * Display a listing of incoming student leads / inquiries for the provider.
     */
    public function index(Request $request): View
    {
        $providerId = Auth::id();
        $status = $request->input('status');
        $propertyId = $request->input('property_id');
        $search = $request->input('search');

        $query = PropertyInquiry::forProvider($providerId)
            ->with(['property.images', 'user'])
            ->latest();

        if ($status && in_array($status, ['new', 'contacted', 'visit_scheduled', 'converted', 'closed'])) {
            $query->where('status', $status);
        }

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate(15)->withQueryString();

        // Get count aggregations
        $counts = [
            'all' => PropertyInquiry::forProvider($providerId)->count(),
            'new' => PropertyInquiry::forProvider($providerId)->where('status', 'new')->count(),
            'contacted' => PropertyInquiry::forProvider($providerId)->where('status', 'contacted')->count(),
            'visit_scheduled' => PropertyInquiry::forProvider($providerId)->where('status', 'visit_scheduled')->count(),
            'converted' => PropertyInquiry::forProvider($providerId)->where('status', 'converted')->count(),
            'closed' => PropertyInquiry::forProvider($providerId)->where('status', 'closed')->count(),
        ];

        $myProperties = Auth::user()->properties()->select('id', 'title')->get();

        return view('provider.leads.index', compact('leads', 'counts', 'status', 'myProperties'));
    }

    /**
     * Update the lead status or provider notes.
     */
    public function update(Request $request, PropertyInquiry $inquiry): RedirectResponse
    {
        if ($inquiry->provider_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,visit_scheduled,converted,closed'],
            'provider_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['status'] === 'contacted' && ! $inquiry->contacted_at) {
            $validated['contacted_at'] = now();
        }

        $inquiry->update($validated);

        return back()->with('success', 'Lead status updated successfully.');
    }
}
