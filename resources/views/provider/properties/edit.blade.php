@extends('layouts.dashboard', ['title' => 'Edit ' . $property->title])

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="editPropertyForm()">

    {{-- Form Header --}}
    <div class="flex items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Accommodation</h1>
            <p class="text-xs sm:text-sm text-slate-500">Update rates, bed vacancy, photos, or amenities for {{ $property->title }}.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('properties.show', $property->slug) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                View Public Listing &rarr;
            </a>
            <a href="{{ route('provider.properties.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                Cancel
            </a>
        </div>
    </div>

    @include('components.alert')

    <form action="{{ route('provider.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- 1. BASIC INFORMATION --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Basic Information</h3>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Property / Listing Title *</label>
                <input type="text" name="title" value="{{ old('title', $property->title) }}" required
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Accommodation Type *</label>
                    <select name="property_type" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                        <option value="pg" {{ old('property_type', $property->property_type) === 'pg' ? 'selected' : '' }}>Paying Guest (PG)</option>
                        <option value="hostel" {{ old('property_type', $property->property_type) === 'hostel' ? 'selected' : '' }}>Student Hostel</option>
                        <option value="room" {{ old('property_type', $property->property_type) === 'room' ? 'selected' : '' }}>Private Room</option>
                        <option value="flat" {{ old('property_type', $property->property_type) === 'flat' ? 'selected' : '' }}>Student Flat / 1BHK / 2BHK</option>
                        <option value="lodge" {{ old('property_type', $property->property_type) === 'lodge' ? 'selected' : '' }}>Lodge / Exam Stay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Gender Preference *</label>
                    <select name="gender_preference" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                        <option value="male" {{ old('gender_preference', $property->gender_preference) === 'male' ? 'selected' : '' }}>Boys Only</option>
                        <option value="female" {{ old('gender_preference', $property->gender_preference) === 'female' ? 'selected' : '' }}>Girls Only</option>
                        <option value="any" {{ old('gender_preference', $property->gender_preference) === 'any' ? 'selected' : '' }}>Co-ed / Any</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Description</label>
                <textarea name="description" rows="4"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">{{ old('description', $property->description) }}</textarea>
            </div>
        </div>

        {{-- 2. LOCATION --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">2</span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Address & Geographic Location</h3>
                </div>

                <button type="button" @click="detectProviderGps()" :disabled="detectingGps"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition border border-indigo-200 shadow-2xs">
                    <span>📍</span>
                    <span x-text="detectingGps ? 'Detecting GPS...' : '📍 Auto-Fill Current GPS Location'"></span>
                </button>
            </div>

            <div x-show="gpsStatus" class="p-2.5 rounded-xl bg-indigo-50/80 border border-indigo-100 text-xs text-indigo-800 font-medium" x-text="gpsStatus"></div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Premise Street Address *</label>
                <input type="text" name="address" value="{{ old('address', $property->address) }}" required
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Locality / Area *</label>
                    <input type="text" name="locality" id="edit-locality-input" value="{{ old('locality', $property->locality) }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">City / District *</label>
                    <input type="text" name="city" id="edit-city-input" value="{{ old('city', $property->city) }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Area Pincode (6 digits)</label>
                    <input type="text" name="pincode" id="edit-pincode-input" value="{{ old('pincode', $property->pincode) }}" maxlength="6"
                        @input="if ($el.value.length === 6) lookupPincode($el.value)"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                    <span class="text-[10px] text-slate-400 mt-1 block">Typing 6-digit pincode automatically auto-fills coordinates</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Latitude (GPS)</label>
                    <input type="number" step="any" id="edit-lat-input" name="latitude" value="{{ old('latitude', $property->latitude) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2 text-xs focus:border-indigo-600 focus:outline-hidden bg-slate-50 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Longitude (GPS)</label>
                    <input type="number" step="any" id="edit-lng-input" name="longitude" value="{{ old('longitude', $property->longitude) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2 text-xs focus:border-indigo-600 focus:outline-hidden bg-slate-50 font-mono">
                </div>
            </div>
        </div>

        {{-- 3. PRICING & DEPOSIT --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">3</span>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Pricing & Capacity</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Min Rent (₹) *</label>
                    <input type="number" name="monthly_rent_min" value="{{ old('monthly_rent_min', $property->monthly_rent_min) }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Max Rent (₹)</label>
                    <input type="number" name="monthly_rent_max" value="{{ old('monthly_rent_max', $property->monthly_rent_max) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Security Deposit (₹)</label>
                    <input type="number" name="security_deposit" value="{{ old('security_deposit', $property->security_deposit) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Notice Period (Days)</label>
                    <input type="number" name="notice_period_days" value="{{ old('notice_period_days', $property->notice_period_days) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Total Bed Capacity</label>
                    <input type="number" name="total_beds" value="{{ old('total_beds', $property->total_beds) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Currently Vacant Beds</label>
                    <input type="number" name="available_beds" value="{{ old('available_beds', $property->available_beds) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>
            </div>
        </div>

        {{-- 4. ROOM VARIANTS (Dynamic Alpine Repeater) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">4</span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Room Configurations</h3>
                </div>
                <button type="button" @click="addRoom()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    + Add Room Option
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(room, index) in rooms" :key="index">
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-3 relative">
                        <input type="hidden" :name="'rooms[' + index + '][id]'" x-model="room.id">

                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-indigo-900" x-text="'Room Variant #' + (index + 1)"></span>
                            <button type="button" @click="removeRoom(index)" x-show="rooms.length > 1" class="text-rose-600 text-xs font-bold hover:underline">
                                Remove
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Room Sharing</label>
                                <select :name="'rooms[' + index + '][room_type]'" x-model="room.room_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                                    <option value="single">Single (Private)</option>
                                    <option value="double">Double Sharing</option>
                                    <option value="triple">Triple Sharing</option>
                                    <option value="four_plus">4+ Sharing</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Title / Label</label>
                                <input type="text" :name="'rooms[' + index + '][title]'" x-model="room.title" required
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Rent per Bed (₹)</label>
                                <input type="number" :name="'rooms[' + index + '][monthly_rent]'" x-model="room.monthly_rent" required
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-1">
                            <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" :name="'rooms[' + index + '][has_attached_bathroom]'" value="1" :checked="room.has_attached_bathroom == 1" @change="room.has_attached_bathroom = $event.target.checked ? 1 : 0" class="rounded text-indigo-600">
                                <span>Attached Bathroom</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" :name="'rooms[' + index + '][has_ac]'" value="1" :checked="room.has_ac == 1" @change="room.has_ac = $event.target.checked ? 1 : 0" class="rounded text-indigo-600">
                                <span>Air Conditioned</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" :name="'rooms[' + index + '][has_balcony]'" value="1" :checked="room.has_balcony == 1" @change="room.has_balcony = $event.target.checked ? 1 : 0" class="rounded text-indigo-600">
                                <span>Balcony</span>
                            </label>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- 5. FOOD & RULES --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">5</span>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Food & House Rules</h3>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer mb-2">
                    <input type="checkbox" name="food_included" value="1" x-model="hasFood" class="rounded text-indigo-600">
                    <span class="text-sm font-bold text-slate-800">Food / Meals Included in Rent</span>
                </label>

                <div class="mt-3" x-show="hasFood" x-transition>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Meal Schedule & Details</label>
                    <textarea name="food_details" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">{{ old('food_details', $property->food_details) }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Gate Curfew Time</label>
                <input type="text" name="gate_closing_time" value="{{ old('gate_closing_time', $property->gate_closing_time) }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Nearby Colleges & Institutes</label>
                    <button type="button" @click="addCollege()" class="text-xs font-bold text-indigo-600">+ Add College</button>
                </div>
                <div class="space-y-2">
                    <template x-for="(college, index) in colleges" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="text" :name="'nearby_colleges[' + index + '][name]'" x-model="college.name" placeholder="College Name"
                                class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            <input type="number" step="0.1" :name="'nearby_colleges[' + index + '][distance_km]'" x-model="college.distance_km" placeholder="Dist (km)"
                                class="w-24 rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            <button type="button" @click="removeCollege(index)" x-show="colleges.length > 1" class="text-rose-500 font-bold text-xs p-1">×</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- 6. AMENITIES --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">6</span>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Amenities</h3>
            </div>

            @php
                $attachedAmenityIds = $property->amenities->pluck('id')->toArray();
            @endphp

            @foreach ($amenities as $category => $items)
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-2.5">{{ ucfirst($category) }}</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($items as $amenity)
                            <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-indigo-50/50 transition text-xs font-medium text-slate-700">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                    {{ in_array($amenity->id, $attachedAmenityIds) ? 'checked' : '' }}
                                    class="rounded text-indigo-600">
                                <span>{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 7. EXISTING & NEW IMAGES --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">7</span>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Property Photos</h3>
            </div>

            {{-- Existing Photos Grid --}}
            @if ($property->images->count() > 0)
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Current Photos ({{ $property->images->count() }})</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach ($property->images as $img)
                            <div class="relative group rounded-2xl overflow-hidden border border-slate-200 shadow-2xs">
                                <img src="{{ $img->url }}" class="h-28 w-full object-cover">

                                @if ($img->is_primary)
                                    <span class="absolute top-2 left-2 bg-emerald-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-md shadow-xs">
                                        Primary
                                    </span>
                                @else
                                    <button type="submit" form="set-primary-{{ $img->id }}"
                                        class="absolute top-2 left-2 bg-slate-900/80 text-white text-[10px] font-bold px-2 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition">
                                        Set Primary
                                    </button>
                                @endif

                                <button type="submit" form="delete-img-{{ $img->id }}"
                                    class="absolute top-2 right-2 bg-rose-600 text-white p-1 rounded-md opacity-0 group-hover:opacity-100 transition hover:bg-rose-700"
                                    title="Delete Photo">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Upload Additional Photos --}}
            <div class="border-2 border-dashed border-slate-300 rounded-3xl p-6 text-center hover:border-indigo-400 transition bg-slate-50/50">
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/jpg" @change="previewImages($event)"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer">
                <p class="mt-2 text-[11px] text-slate-400">Upload additional JPG, PNG, or WEBP photos (max 5MB each).</p>
            </div>

            <div class="flex flex-wrap gap-3 pt-2" x-show="imagePreviews.length > 0">
                <template x-for="(src, idx) in imagePreviews" :key="idx">
                    <div class="relative h-20 w-28 rounded-xl overflow-hidden border border-slate-200 shadow-2xs">
                        <img :src="src" class="w-full h-full object-cover">
                    </div>
                </template>
            </div>
        </div>

        {{-- 8. 360° LIVE VIRTUAL TOUR ROOM PHOTO (Interactive 360) --}}
        <div class="rounded-3xl border border-indigo-200 bg-gradient-to-br from-indigo-50/40 via-white to-purple-50/40 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-indigo-100">
                <div class="flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">8</span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-indigo-950 flex items-center gap-2">
                        <span>🔄 Live 360° Virtual Tour Room Photo</span>
                        <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-600 text-white font-extrabold">Feature</span>
                    </h3>
                </div>
                <span class="text-xs font-bold text-indigo-600">360° Student Preview</span>
            </div>

            @if ($property->image_360)
                <div class="p-4 rounded-2xl bg-white border border-indigo-100">
                    <p class="text-xs font-bold text-slate-700 mb-2">Current 360° Panorama Photo:</p>
                    <div class="relative h-44 w-full max-w-md rounded-2xl overflow-hidden border border-indigo-200 shadow-xs">
                        <img src="{{ $property->image_360_url }}" class="w-full h-full object-cover">
                        <span class="absolute top-2 left-2 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                            Active 360° Room Photo
                        </span>
                    </div>
                </div>
            @endif

            <p class="text-xs text-slate-600 leading-relaxed">
                {{ $property->image_360 ? 'Upload a new photo to replace the current 360° panorama tour:' : 'Upload a 360° panoramic photo of the room taken from the center. Students can rotate and look around in 360°!' }}
            </p>

            <div class="border-2 border-dashed border-indigo-300 rounded-3xl p-6 text-center hover:border-indigo-500 transition bg-white/80">
                <input type="file" name="image_360" accept="image/jpeg,image/png,image/webp,image/jpg" capture="environment" @change="preview360($event)"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer">
                <p class="mt-2 text-[11px] text-slate-400">JPG, PNG, WEBP up to 15MB. Equirectangular or 360 mobile camera panorama.</p>
            </div>

            <div x-show="image360Preview" class="pt-2">
                <p class="text-xs font-bold text-slate-700 mb-2">New 360° Photo Preview:</p>
                <div class="relative h-44 w-full max-w-md rounded-2xl overflow-hidden border border-indigo-200 shadow-md">
                    <img :src="image360Preview" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        {{-- SAVE CHANGES BUTTON --}}
        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('provider.properties.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-indigo-600 px-8 py-3 text-xs font-extrabold text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
                Save & Update Accommodation
            </button>
        </div>

    </form>

    {{-- Hidden Action Forms for Image Delete and Set Primary --}}
    @foreach ($property->images as $img)
        <form id="delete-img-{{ $img->id }}" method="POST" action="{{ route('provider.properties.images.delete', [$property->id, $img->id]) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <form id="set-primary-{{ $img->id }}" method="POST" action="{{ route('provider.properties.images.primary', [$property->id, $img->id]) }}" class="hidden">
            @csrf
        </form>
    @endforeach

</div>
@endsection

@push('scripts')
<script>
function editPropertyForm() {
    return {
        hasFood: {{ $property->food_included ? 'true' : 'false' }},
        rooms: {!! json_encode($property->rooms->map(fn($r) => [
            'id' => $r->id,
            'room_type' => $r->room_type,
            'title' => $r->title,
            'monthly_rent' => $r->monthly_rent,
            'security_deposit' => $r->security_deposit,
            'total_capacity' => $r->total_capacity,
            'available_capacity' => $r->available_capacity,
            'has_attached_bathroom' => $r->has_attached_bathroom ? 1 : 0,
            'has_ac' => $r->has_ac ? 1 : 0,
            'has_balcony' => $r->has_balcony ? 1 : 0,
        ])) !!},
        colleges: {!! json_encode(!empty($property->nearby_colleges) ? $property->nearby_colleges : [['name' => '', 'distance_km' => '']]) !!},
        imagePreviews: [],
        image360Preview: null,
        detectingGps: false,
        gpsStatus: '',

        addRoom() {
            this.rooms.push({ id: null, room_type: 'double', title: '', monthly_rent: '', security_deposit: '', total_capacity: 2, available_capacity: 1, has_attached_bathroom: 0, has_ac: 0, has_balcony: 0 });
        },
        removeRoom(index) {
            if (this.rooms.length > 1) this.rooms.splice(index, 1);
        },
        addCollege() {
            this.colleges.push({ name: '', distance_km: '' });
        },
        removeCollege(index) {
            if (this.colleges.length > 1) this.colleges.splice(index, 1);
        },
        previewImages(event) {
            this.imagePreviews = [];
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                this.imagePreviews.push(URL.createObjectURL(files[i]));
            }
        },
        preview360(event) {
            const file = event.target.files[0];
            if (file) {
                this.image360Preview = URL.createObjectURL(file);
            }
        },
        detectProviderGps() {
            if (!('geolocation' in navigator)) {
                alert('Geolocation is not supported by your browser.');
                return;
            }
            this.detectingGps = true;
            this.gpsStatus = 'Fetching device GPS coordinates...';
            const self = this;
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    document.getElementById('edit-lat-input').value = pos.coords.latitude.toFixed(6);
                    document.getElementById('edit-lng-input').value = pos.coords.longitude.toFixed(6);
                    self.gpsStatus = 'Coordinates updated: ' + pos.coords.latitude.toFixed(4) + ', ' + pos.coords.longitude.toFixed(4);
                    self.detectingGps = false;

                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&zoom=14', { headers: { 'Accept': 'application/json' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            const city = (data.address && (data.address.city || data.address.town || data.address.district)) || '';
                            const area = (data.address && (data.address.suburb || data.address.neighbourhood)) || '';
                            const pin = (data.address && data.address.postcode) || '';
                            const cityEl = document.getElementById('edit-city-input');
                            const locEl = document.getElementById('edit-locality-input');
                            const pinEl = document.getElementById('edit-pincode-input');
                            if (city && cityEl && !cityEl.value) cityEl.value = city;
                            if (area && locEl && !locEl.value) locEl.value = area;
                            if (pin && pinEl && !pinEl.value) pinEl.value = pin;
                        })
                        .catch(function() {});
                },
                function(err) {
                    self.detectingGps = false;
                    self.gpsStatus = 'Could not access GPS. Please enter Pincode or City.';
                },
                { timeout: 8000 }
            );
        },
        lookupPincode(pincode) {
            if (!pincode || pincode.length !== 6) return;
            const self = this;
            this.gpsStatus = 'Looking up pincode ' + pincode + '...';

            fetch('https://nominatim.openstreetmap.org/search?postalcode=' + pincode + '&country=india&format=json&limit=1', { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data && data.length > 0) {
                        document.getElementById('edit-lat-input').value = parseFloat(data[0].lat).toFixed(6);
                        document.getElementById('edit-lng-input').value = parseFloat(data[0].lon).toFixed(6);
                        self.gpsStatus = 'Coordinates auto-filled for pincode ' + pincode + '!';
                    }
                })
                .catch(function() {});

            fetch('https://api.postalpincode.in/pincode/' + pincode)
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res && res[0] && res[0].PostOffice && res[0].PostOffice.length > 0) {
                        const po = res[0].PostOffice[0];
                        const cityEl = document.getElementById('edit-city-input');
                        if (cityEl && !cityEl.value) {
                            cityEl.value = po.District || po.Block || po.Circle;
                        }
                    }
                })
                .catch(function() {});
        }
    };
}
</script>
@endpush
