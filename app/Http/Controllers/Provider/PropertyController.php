<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display a listing of the provider's properties.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');

        $query = $request->user()
            ->properties()
            ->with(['images', 'rooms', 'amenities'])
            ->latest();

        if ($status && in_array($status, ['active', 'paused', 'draft'])) {
            $query->where('status', $status);
        }

        $properties = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => $request->user()->properties()->count(),
            'active' => $request->user()->properties()->where('status', 'active')->count(),
            'paused' => $request->user()->properties()->where('status', 'paused')->count(),
        ];

        return view('provider.properties.index', compact('properties', 'counts', 'status'));
    }

    /**
     * Show the form for creating a new accommodation listing.
     */
    public function create(): View
    {
        $this->authorize('create', Property::class);

        $amenities = Amenity::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('provider.properties.create', compact('amenities'));
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $property = DB::transaction(function () use ($validated, $request) {
            // Generate unique slug
            $baseSlug = Str::slug($validated['title'] . '-' . $validated['city']);
            $slug = $baseSlug;
            $counter = 1;
            while (Property::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            // Extract nested relations
            $amenityIds = $validated['amenities'] ?? [];
            $roomsData = $validated['rooms'] ?? [];
            unset($validated['amenities'], $validated['rooms'], $validated['images']);

            // Create property
            $validated['user_id'] = $request->user()->id;
            $validated['slug'] = $slug;
            $validated['status'] = 'active';

            // Filter nearby colleges empty rows
            if (! empty($validated['nearby_colleges'])) {
                $validated['nearby_colleges'] = array_values(array_filter($validated['nearby_colleges'], fn($c) => ! empty($c['name'])));
            }

            // Auto-resolve GPS coordinates if missing or defaulted to Pune
            $isDefaultPune = isset($validated['latitude'], $validated['longitude'])
                && abs((float)$validated['latitude'] - 18.5679) < 0.001
                && abs((float)$validated['longitude'] - 73.9143) < 0.001;

            if (empty($validated['latitude']) || empty($validated['longitude']) || ($isDefaultPune && strtolower(trim($validated['city'] ?? '')) !== 'pune')) {
                $coords = \App\Services\Geocoding\LocationService::resolveCoordinates(
                    $validated['city'] ?? null,
                    $validated['locality'] ?? null,
                    $validated['pincode'] ?? null
                );
                if ($coords) {
                    $validated['latitude'] = $coords[0];
                    $validated['longitude'] = $coords[1];
                }
            }

            // Filter rules empty rows
            if (! empty($validated['rules'])) {
                $validated['rules'] = array_values(array_filter($validated['rules'], fn($r) => ! empty(trim($r))));
            }

            $property = Property::create($validated);

            // Sync amenities
            if (! empty($amenityIds)) {
                $property->amenities()->sync($amenityIds);
            }

            // Create rooms
            if (! empty($roomsData)) {
                foreach ($roomsData as $room) {
                    $room['property_id'] = $property->id;
                    PropertyRoom::create($room);
                }
            }

            // Handle uploaded images
            if ($request->hasFile('images')) {
                $isFirst = true;
                foreach ($request->file('images') as $file) {
                    $path = $file->store("properties/{$property->id}", 'public');

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $path,
                        'is_primary' => $isFirst,
                    ]);

                    $isFirst = false;
                }
            }

            // Handle uploaded 360 panorama image
            if ($request->hasFile('image_360')) {
                $path360 = $request->file('image_360')->store("properties/{$property->id}/360", 'public');
                $property->update(['image_360' => $path360]);
            }

            return $property;
        });

        return redirect()->route('provider.properties.index')
            ->with('success', 'Property "' . $property->title . '" has been published successfully!');
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        $property->load(['images', 'rooms', 'amenities']);
        $amenities = Amenity::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('provider.properties.edit', compact('property', 'amenities'));
    }

    /**
     * Update the specified property in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $property) {
            $amenityIds = $validated['amenities'] ?? [];
            $roomsData = $validated['rooms'] ?? [];
            unset($validated['amenities'], $validated['rooms'], $validated['images']);

            // Filter nearby colleges empty rows
            if (! empty($validated['nearby_colleges'])) {
                $validated['nearby_colleges'] = array_values(array_filter($validated['nearby_colleges'], fn($c) => ! empty($c['name'])));
            }

            // Filter rules empty rows
            if (! empty($validated['rules'])) {
                $validated['rules'] = array_values(array_filter($validated['rules'], fn($r) => ! empty(trim($r))));
            }

            // Auto-resolve GPS coordinates if missing or defaulted to Pune
            $isDefaultPune = isset($validated['latitude'], $validated['longitude'])
                && abs((float)$validated['latitude'] - 18.5679) < 0.001
                && abs((float)$validated['longitude'] - 73.9143) < 0.001;

            if (empty($validated['latitude']) || empty($validated['longitude']) || ($isDefaultPune && strtolower(trim($validated['city'] ?? '')) !== 'pune')) {
                $coords = \App\Services\Geocoding\LocationService::resolveCoordinates(
                    $validated['city'] ?? null,
                    $validated['locality'] ?? null,
                    $validated['pincode'] ?? null
                );
                if ($coords) {
                    $validated['latitude'] = $coords[0];
                    $validated['longitude'] = $coords[1];
                }
            }

            $property->update($validated);

            // Sync amenities
            $property->amenities()->sync($amenityIds);

            // Update/create rooms
            if (! empty($roomsData)) {
                $existingRoomIds = [];
                foreach ($roomsData as $room) {
                    if (! empty($room['id'])) {
                        $existing = PropertyRoom::where('id', $room['id'])->where('property_id', $property->id)->first();
                        if ($existing) {
                            $existing->update($room);
                            $existingRoomIds[] = $existing->id;
                            continue;
                        }
                    }

                    $room['property_id'] = $property->id;
                    $newRoom = PropertyRoom::create($room);
                    $existingRoomIds[] = $newRoom->id;
                }

                // Delete rooms that were removed by provider
                $property->rooms()->whereNotIn('id', $existingRoomIds)->delete();
            }

            // Handle new uploaded images
            if ($request->hasFile('images')) {
                $hasExistingPrimary = $property->images()->where('is_primary', true)->exists();
                $isFirst = ! $hasExistingPrimary;

                foreach ($request->file('images') as $file) {
                    $path = $file->store("properties/{$property->id}", 'public');

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $path,
                        'is_primary' => $isFirst,
                    ]);

                    $isFirst = false;
                }
            }

            // Handle updated 360 panorama image
            if ($request->hasFile('image_360')) {
                $path360 = $request->file('image_360')->store("properties/{$property->id}/360", 'public');
                $property->update(['image_360' => $path360]);
            }
        });

        return redirect()->route('provider.properties.index')
            ->with('success', 'Property "' . $property->title . '" updated successfully!');
    }

    /**
     * Toggle property active/paused status.
     */
    public function toggleStatus(Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $newStatus = $property->status === 'active' ? 'paused' : 'active';
        $property->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? 'Listing is now live and visible to students.'
            : 'Listing has been paused and hidden from student search.';

        return back()->with('success', $message);
    }

    /**
     * Delete an image from a property.
     */
    public function deleteImage(Property $property, PropertyImage $image): RedirectResponse
    {
        $this->authorize('update', $property);

        if ($image->property_id !== $property->id) {
            abort(403);
        }

        // Delete physical file if stored locally
        if (! str_starts_with($image->image_path, 'http')) {
            Storage::disk('public')->delete($image->image_path);
        }

        $wasPrimary = $image->is_primary;
        $image->delete();

        // If primary was deleted, set next image as primary
        if ($wasPrimary) {
            $next = $property->images()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Image deleted successfully.');
    }

    /**
     * Set an image as primary.
     */
    public function setPrimaryImage(Property $property, PropertyImage $image): RedirectResponse
    {
        $this->authorize('update', $property);

        if ($image->property_id !== $property->id) {
            abort(403);
        }

        $property->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary photo updated.');
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        return redirect()->route('provider.properties.index')
            ->with('success', 'Property listing removed successfully.');
    }
}
