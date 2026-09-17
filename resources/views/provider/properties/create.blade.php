@extends('layouts.dashboard', ['title' => 'List New Student Accommodation'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="propertyForm()">

    {{-- Form Header --}}
    <div class="flex items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">List a Student Accommodation</h1>
            <p class="text-xs sm:text-sm text-slate-500">Provide accurate property details to get verified and attract student inquiries.</p>
        </div>

        <a href="{{ route('provider.properties.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
            Cancel
        </a>
    </div>

    @include('components.alert')

    <form action="{{ route('provider.properties.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- 1. BASIC INFORMATION --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Basic Property Information</h3>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Property / Listing Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g., Anna Lodge & Luxury Student PG"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Accommodation Type *</label>
                    <select name="property_type" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                        <option value="pg" {{ old('property_type') === 'pg' ? 'selected' : '' }}>Paying Guest (PG)</option>
                        <option value="hostel" {{ old('property_type') === 'hostel' ? 'selected' : '' }}>Student Hostel</option>
                        <option value="room" {{ old('property_type') === 'room' ? 'selected' : '' }}>Private Room</option>
                        <option value="flat" {{ old('property_type') === 'flat' ? 'selected' : '' }}>Student Flat / 1BHK / 2BHK</option>
                        <option value="lodge" {{ old('property_type') === 'lodge' ? 'selected' : '' }}>Lodge / Exam Stay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Gender Preference *</label>
                    <select name="gender_preference" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                        <option value="male" {{ old('gender_preference') === 'male' ? 'selected' : '' }}>Boys Only</option>
                        <option value="female" {{ old('gender_preference') === 'female' ? 'selected' : '' }}>Girls Only</option>
                        <option value="any" {{ old('gender_preference') === 'any' ? 'selected' : '' }}>Co-ed / Any</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Description</label>
                <textarea name="description" rows="4" placeholder="Describe the ambiance, safety measures, cleanliness, study atmosphere, and special highlights..."
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- 2. LOCATION & MAP GPS --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">2</span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Address & Geographic Location</h3>
                </div>

                {{-- Quick GPS detection button --}}
                <button type="button" @click="detectProviderGps()" :disabled="detectingGps"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition border border-indigo-200 shadow-2xs">
                    <span>📍</span>
                    <span x-text="detectingGps ? 'Detecting Device GPS...' : '📍 Auto-Fill Current GPS Location'"></span>
                </button>
            </div>

            <div x-show="gpsStatus" class="p-2.5 rounded-xl bg-indigo-50/80 border border-indigo-100 text-xs text-indigo-800 font-medium" x-text="gpsStatus"></div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Premise Street Address *</label>
                <input type="text" name="address" value="{{ old('address') }}" required placeholder="e.g., Plot 42, Sindur, Near Vinoba Bhave University Campus"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Locality / Area *</label>
                    <input type="text" name="locality" id="locality-input" value="{{ old('locality') }}" required placeholder="e.g., Sindur or Vinoba Nagar"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">City / District *</label>
                    <input type="text" name="city" id="city-input" value="{{ old('city') }}" required placeholder="e.g., Hazaribagh, Pune, Kota"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Area Pincode (6 digits) *</label>
                    <input type="text" name="pincode" id="pincode-input" value="{{ old('pincode') }}" maxlength="6"
                        @input="if ($el.value.length === 6) lookupPincode($el.value)"
                        placeholder="e.g., 825301"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                    <span class="text-[10px] text-slate-400 mt-1 block">Typing 6-digit pincode automatically auto-fills coordinates</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Latitude (GPS)</label>
                    <input type="number" step="any" id="lat-input" name="latitude" value="{{ old('latitude') }}" placeholder="Auto-calculated (e.g. 23.9925)"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2 text-xs focus:border-indigo-600 focus:outline-hidden bg-slate-50 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Longitude (GPS)</label>
                    <input type="number" step="any" id="lng-input" name="longitude" value="{{ old('longitude') }}" placeholder="Auto-calculated (e.g. 85.3637)"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2 text-xs focus:border-indigo-600 focus:outline-hidden bg-slate-50 font-mono">
                </div>
            </div>
        </div>

        {{-- 3. PRICING & DEPOSIT --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">3</span>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Pricing & Terms</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Min Monthly Rent (₹) *</label>
                    <input type="number" name="monthly_rent_min" value="{{ old('monthly_rent_min', 1500) }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Max Monthly Rent (₹)</label>
                    <input type="number" name="monthly_rent_max" value="{{ old('monthly_rent_max', 7000) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Security Deposit (₹)</label>
                    <input type="number" name="security_deposit" value="{{ old('security_deposit', 2000) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Notice Period (Days)</label>
                    <input type="number" name="notice_period_days" value="{{ old('notice_period_days', 30) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Total Bed Capacity</label>
                    <input type="number" name="total_beds" value="{{ old('total_beds', 15) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Currently Vacant Beds</label>
                    <input type="number" name="available_beds" value="{{ old('available_beds', 3) }}"
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
                                    <option value="four_plus">4+ Sharing / Dormitory</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Title / Label</label>
                                <input type="text" :name="'rooms[' + index + '][title]'" x-model="room.title" placeholder="e.g., Double Sharing with Balcony" required
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Monthly Rent per Bed (₹)</label>
                                <input type="number" :name="'rooms[' + index + '][monthly_rent]'" x-model="room.monthly_rent" placeholder="e.g., 2500" required
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-1">
                            <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" :name="'rooms[' + index + '][has_attached_bathroom]'" value="1" x-model="room.has_attached_bathroom" class="rounded text-indigo-600">
                                <span>Attached Bathroom</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" :name="'rooms[' + index + '][has_ac]'" value="1" x-model="room.has_ac" class="rounded text-indigo-600">
                                <span>Air Conditioned</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" :name="'rooms[' + index + '][has_balcony]'" value="1" x-model="room.has_balcony" class="rounded text-indigo-600">
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
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="food_included" value="1" x-model="hasFood" class="rounded text-indigo-600">
                    <span class="text-sm font-bold text-slate-800">Food / Meals Included in Rent</span>
                </label>

                <div x-show="hasFood" class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Food Menu & Inclusions</label>
                    <textarea name="food_details" rows="2" placeholder="e.g., Breakfast, Lunch & Dinner included. Pure vegetarian home-cooked meals."
                        class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-indigo-600 focus:outline-hidden">{{ old('food_details') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Gate Closing / Curfew Time</label>
                <input type="text" name="gate_closing_time" value="{{ old('gate_closing_time', '10:30 PM') }}" placeholder="e.g., 10:30 PM or No Curfew"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-hidden">
            </div>

            {{-- Nearby Colleges Repeater --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Nearby Colleges & Institutes</label>
                    <button type="button" @click="addCollege()" class="text-xs font-bold text-indigo-600">+ Add College</button>
                </div>
                <div class="space-y-2">
                    <template x-for="(college, index) in colleges" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="text" :name="'nearby_colleges[' + index + '][name]'" x-model="college.name" placeholder="College Name (e.g., Vinoba Bhave University, Kolghati)"
                                class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            <input type="number" step="0.1" :name="'nearby_colleges[' + index + '][distance_km]'" x-model="college.distance_km" placeholder="Dist (km)"
                                class="w-24 rounded-xl border border-slate-300 px-3 py-2 text-xs">
                            <button type="button" @click="removeCollege(index)" x-show="colleges.length > 1" class="text-rose-500 font-bold text-xs p-1">×</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- 6. AMENITIES SELECTION --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">6</span>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Amenities & Inclusions</h3>
            </div>

            @foreach ($amenities as $category => $items)
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-2.5">{{ ucfirst($category) }}</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($items as $amenity)
                            <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-indigo-50/50 transition text-xs font-medium text-slate-700">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded text-indigo-600">
                                <span>{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 7. PROPERTY PHOTOS UPLOAD (2-5 Photos) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">7</span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Upload Property Photos (2 - 5 Photos)</h3>
                </div>
                <span class="text-xs font-bold text-slate-400">Bed, Washroom, Study Area</span>
            </div>

            <div class="border-2 border-dashed border-slate-300 rounded-3xl p-8 text-center hover:border-indigo-400 transition bg-slate-50/50">
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/jpg" @change="previewImages($event)"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer">
                <p class="mt-2 text-[11px] text-slate-400">Select 2 to 5 photos (Bedroom, study desk, bathroom, entrance). JPG, PNG, WEBP up to 5MB each.</p>
            </div>

            {{-- Client-side preview thumbnails --}}
            <div class="flex flex-wrap gap-3 pt-2" x-show="imagePreviews.length > 0">
                <template x-for="(src, idx) in imagePreviews" :key="idx">
                    <div class="relative h-24 w-36 rounded-2xl overflow-hidden border border-slate-200 shadow-xs">
                        <img :src="src" class="w-full h-full object-cover">
                        <span class="absolute bottom-1 left-1 bg-slate-900/80 text-white text-[10px] font-bold px-1.5 py-0.5 rounded" x-text="idx === 0 ? 'Primary' : 'Photo ' + (idx + 1)"></span>
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
                <span class="text-xs font-bold text-indigo-600">360° Viewer for Students</span>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Upload a <strong>360° panoramic photo</strong> of the room taken from the center. Students visiting your listing can <strong>rotate and look around in 360°</strong> before contacting you!
            </p>

            <div class="border-2 border-dashed border-indigo-300 rounded-3xl p-6 text-center hover:border-indigo-500 transition bg-white/80">
                <input type="file" name="image_360" accept="image/jpeg,image/png,image/webp,image/jpg" capture="environment" @change="preview360($event)"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer">
                <p class="mt-2 text-[11px] text-slate-400">Upload equirectangular or panorama photo (up to 15MB). You can take a panorama directly on your mobile camera!</p>
            </div>

            {{-- 360 preview --}}
            <div x-show="image360Preview" class="pt-2">
                <p class="text-xs font-bold text-slate-700 mb-2">360° Room Photo Preview:</p>
                <div class="relative h-48 w-full max-w-md rounded-2xl overflow-hidden border border-indigo-200 shadow-md">
                    <img :src="image360Preview" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-slate-900/30 flex items-center justify-center">
                        <span class="px-3 py-1.5 rounded-full bg-white/90 text-indigo-900 font-extrabold text-xs shadow-md flex items-center gap-1.5">
                            <span>🔄</span> 360° Panorama Ready
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUBMIT BUTTON --}}
        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('provider.properties.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-indigo-600 px-8 py-3 text-xs font-extrabold text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
                Publish Accommodation Listing
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
function propertyForm() {
    return {
        hasFood: false,
        rooms: [
            { room_type: 'double', title: 'Standard Double Sharing', monthly_rent: '', security_deposit: '', total_capacity: 2, available_capacity: 2, has_attached_bathroom: 1, has_ac: 0, has_balcony: 0 }
        ],
        colleges: [
            { name: '', distance_km: '' }
        ],
        rules: [''],
        imagePreviews: [],
        image360Preview: null,
        detectingGps: false,
        gpsStatus: '',

        addRoom() {
            this.rooms.push({ room_type: 'double', title: '', monthly_rent: '', security_deposit: '', total_capacity: 2, available_capacity: 1, has_attached_bathroom: 0, has_ac: 0, has_balcony: 0 });
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
        addRule() {
            this.rules.push('');
        },
        removeRule(index) {
            if (this.rules.length > 1) this.rules.splice(index, 1);
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
                    document.getElementById('lat-input').value = pos.coords.latitude.toFixed(6);
                    document.getElementById('lng-input').value = pos.coords.longitude.toFixed(6);
                    self.gpsStatus = 'Coordinates set: ' + pos.coords.latitude.toFixed(4) + ', ' + pos.coords.longitude.toFixed(4);
                    self.detectingGps = false;

                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&zoom=14', { headers: { 'Accept': 'application/json' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            const city = (data.address && (data.address.city || data.address.town || data.address.district)) || '';
                            const area = (data.address && (data.address.suburb || data.address.neighbourhood)) || '';
                            const pin = (data.address && data.address.postcode) || '';
                            const cityEl = document.getElementById('city-input');
                            const locEl = document.getElementById('locality-input');
                            const pinEl = document.getElementById('pincode-input');
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
                        document.getElementById('lat-input').value = parseFloat(data[0].lat).toFixed(6);
                        document.getElementById('lng-input').value = parseFloat(data[0].lon).toFixed(6);
                        self.gpsStatus = 'Coordinates auto-filled for pincode ' + pincode + '!';
                    }
                })
                .catch(function() {});

            fetch('https://api.postalpincode.in/pincode/' + pincode)
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res && res[0] && res[0].PostOffice && res[0].PostOffice.length > 0) {
                        const po = res[0].PostOffice[0];
                        const cityEl = document.getElementById('city-input');
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
