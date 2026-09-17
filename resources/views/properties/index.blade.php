@extends('layouts.app', ['title' => 'Search Student Stays - StayFinder'])

@push('styles')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        .leaflet-popup-content-wrapper {
            border-radius: 1.25rem;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            padding: 0.25rem;
        }
        .leaflet-popup-content {
            margin: 0.5rem;
        }
        .custom-map-pill {
            background: transparent;
            border: none;
        }
    </style>
@endpush

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8"
    x-data="staysExplorer()"
    x-init="init()">
    
    {{-- Search Bar Header --}}
    <div class="mb-4 rounded-3xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <form action="{{ route('properties.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            
            {{-- Preserve View Mode --}}
            <input type="hidden" name="view" :value="viewMode">

            {{-- Search text --}}
            <div class="md:col-span-5 relative">
                <span class="absolute left-3.5 top-3 text-indigo-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="query" id="stays-search-input" value="{{ request('query') }}"
                    placeholder="Search city, area, college (e.g. Pune, Kothrud, COEP, DU)"
                    class="w-full rounded-2xl border border-slate-200 pl-11 pr-4 py-2.5 text-sm font-semibold placeholder-slate-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 focus:outline-hidden">
            </div>

            {{-- City dropdown --}}
            <div class="md:col-span-3">
                <select name="city" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 focus:border-indigo-600 focus:outline-hidden">
                    <option value="">All Cities</option>
                    @foreach ($allCities as $cityName)
                        <option value="{{ $cityName }}" {{ request('city') === $cityName ? 'selected' : '' }}>
                            {{ $cityName }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Hidden GPS fields --}}
            <input type="hidden" name="lat" id="stays-geo-lat" value="{{ request('lat') }}">
            <input type="hidden" name="lng" id="stays-geo-lng" value="{{ request('lng') }}">
            <input type="hidden" name="distance" value="15">

            {{-- Submit and Near Me buttons --}}
            <div class="md:col-span-4 flex gap-2">
                <button type="button" id="stays-near-me-btn" onclick="detectStaysNearMe(this)"
                    class="rounded-2xl border border-indigo-200 bg-indigo-50/70 px-3 py-2.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition shrink-0 flex items-center gap-1.5"
                    title="Detect Current Location & Find Nearby PGs">
                    <span>📍</span>
                    <span id="stays-near-me-text">Near Me</span>
                </button>

                <button type="submit" class="flex-1 rounded-2xl bg-indigo-600 py-2.5 px-4 text-xs font-bold text-white shadow-md hover:bg-indigo-700 transition flex items-center justify-center gap-1.5">
                    Search Stays
                </button>

                {{-- Mobile Filter Trigger --}}
                <button type="button" @click="mobileFiltersOpen = true"
                    class="lg:hidden rounded-2xl border border-slate-300 px-3.5 py-2.5 text-slate-700 hover:bg-slate-50 transition flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span class="text-xs font-bold">Filters</span>
                </button>
            </div>
        </form>
    </div>

    {{-- College Proximity Quick-Filter Bar --}}
    <div class="mb-6 flex items-center gap-2 overflow-x-auto pb-2 text-xs">
        <span class="font-extrabold text-slate-400 shrink-0 uppercase tracking-wider text-[11px]">🎓 Near Campus:</span>
        @foreach ($topColleges as $college)
            <a href="{{ route('properties.index', ['query' => $college['query'], 'city' => $college['city']]) }}"
                class="rounded-xl px-3 py-1.5 font-bold transition shrink-0 {{ request('query') === $college['query'] ? 'bg-indigo-600 text-white shadow-xs' : 'border border-slate-200 bg-white text-slate-700 hover:border-indigo-300 hover:text-indigo-600' }}">
                {{ $college['name'] }}
            </a>
        @endforeach
    </div>

    {{-- Layout Grid: Sidebar Filters + Listings --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- =========================================================
            DESKTOP FILTERS SIDEBAR
        ========================================================== --}}
        <aside class="hidden lg:block lg:col-span-3 rounded-3xl border border-slate-200 bg-white p-6 shadow-xs sticky top-24">
            <form action="{{ route('properties.index') }}" method="GET" class="space-y-6">
                
                {{-- Preserve Query and View Mode --}}
                @if (request('query'))
                    <input type="hidden" name="query" value="{{ request('query') }}">
                @endif
                <input type="hidden" name="view" :value="viewMode">

                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Filters</h3>
                    <a href="{{ route('properties.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">
                        Reset All
                    </a>
                </div>

                {{-- Accommodation Type --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Accommodation Type</label>
                    <div class="space-y-2 text-xs font-medium text-slate-600">
                        @php
                            $types = ['pg' => 'Paying Guest (PG)', 'hostel' => 'Student Hostel', 'room' => 'Private Room', 'flat' => 'Student Flat', 'lodge' => 'Lodge / Exam Stay'];
                        @endphp
                        @foreach ($types as $key => $label)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-slate-900">
                                <input type="radio" name="type" value="{{ $key }}" {{ request('type') === $key ? 'checked' : '' }}
                                    class="rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Gender Preference --}}
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Gender Preference</label>
                    <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-xl text-xs font-bold text-center">
                        <label class="cursor-pointer">
                            <input type="radio" name="gender" value="any" {{ request('gender', 'any') === 'any' ? 'checked' : '' }} class="sr-only peer">
                            <div class="py-1.5 rounded-lg peer-checked:bg-white peer-checked:text-indigo-700 peer-checked:shadow-xs text-slate-600 transition">Any</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="gender" value="male" {{ request('gender') === 'male' ? 'checked' : '' }} class="sr-only peer">
                            <div class="py-1.5 rounded-lg peer-checked:bg-white peer-checked:text-indigo-700 peer-checked:shadow-xs text-slate-600 transition">Boys</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="gender" value="female" {{ request('gender') === 'female' ? 'checked' : '' }} class="sr-only peer">
                            <div class="py-1.5 rounded-lg peer-checked:bg-white peer-checked:text-indigo-700 peer-checked:shadow-xs text-slate-600 transition">Girls</div>
                        </label>
                    </div>
                </div>

                {{-- Budget Maximum --}}
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Max Rent / Budget</label>
                    <div class="space-y-1.5 text-xs text-slate-600">
                        @php
                            $budgets = [1500 => 'Under ₹1,500 (Budget/Dorm)', 3000 => 'Under ₹3,000', 5000 => 'Under ₹5,000', 8000 => 'Under ₹8,000', 12000 => 'Under ₹12,000', 20000 => 'Under ₹20,000'];
                        @endphp
                        @foreach ($budgets as $val => $txt)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-slate-900">
                                <input type="radio" name="budget" value="{{ $val }}" {{ request('budget') == $val ? 'checked' : '' }}
                                    class="rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $txt }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Room Sharing Type --}}
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Sharing Type</label>
                    <div class="space-y-1.5 text-xs text-slate-600">
                        @php
                            $roomTypes = ['single' => 'Single Occupancy (Private)', 'double' => 'Double Sharing (2 Beds)', 'triple' => 'Triple Sharing (3 Beds)', 'four_plus' => '4+ Sharing'];
                        @endphp
                        @foreach ($roomTypes as $val => $txt)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-slate-900">
                                <input type="radio" name="room_type" value="{{ $val }}" {{ request('room_type') === $val ? 'checked' : '' }}
                                    class="rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $txt }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Food Included --}}
                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="food" value="1" {{ request('food') ? 'checked' : '' }}
                            class="rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-slate-800">Only Stays with Meals (Mess)</span>
                    </label>
                </div>

                {{-- Distance Radius --}}
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Distance From College / Landmark</label>
                    <select name="distance" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-indigo-600 focus:outline-hidden">
                        <option value="">Any distance</option>
                        <option value="1" {{ request('distance') == '1' ? 'selected' : '' }}>Within 1 km (Walking)</option>
                        <option value="3" {{ request('distance') == '3' ? 'selected' : '' }}>Within 3 km</option>
                        <option value="5" {{ request('distance') == '5' ? 'selected' : '' }}>Within 5 km</option>
                        <option value="10" {{ request('distance') == '10' ? 'selected' : '' }}>Within 10 km</option>
                    </select>
                </div>

                {{-- Popular Student Amenities --}}
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Amenities</label>
                    <div class="space-y-1.5 text-xs text-slate-600 max-h-48 overflow-y-auto pr-1">
                        @foreach ($popularAmenities as $amenity)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-slate-900">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                    {{ in_array($amenity->id, (array) request('amenities', [])) ? 'checked' : '' }}
                                    class="rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full rounded-2xl bg-indigo-600 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-indigo-700 transition">
                        Apply Filters
                    </button>
                </div>

            </form>
        </aside>

        {{-- =========================================================
            LISTINGS RESULTS COLUMN
        ========================================================== --}}
        <main class="lg:col-span-9 space-y-6">
            
            {{-- Results Header, Sort Options & View Switcher --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <h1 class="text-xl font-black text-slate-900">
                        @if (request('query'))
                            Stays in "{{ request('query') }}"
                        @elseif (request('city'))
                            Student Stays in {{ request('city') }}
                        @else
                            All Student Accommodations
                        @endif
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Found <strong class="text-slate-900">{{ $properties->total() }}</strong> verified stays matching your criteria
                    </p>
                    @if (! empty($proximityNote))
                        <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-900 shadow-2xs">
                            <span>📍</span>
                            <span>{{ $proximityNote }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- View Mode Toggle: Grid vs Map --}}
                    <div class="flex items-center p-1 bg-slate-200/80 rounded-2xl text-xs font-bold">
                        <button type="button" @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1">
                            <span>🔲 Grid View</span>
                        </button>
                        <button type="button" @click="viewMode = 'map'; initMap();"
                            :class="viewMode === 'map' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1">
                            <span>🗺️ Map View</span>
                        </button>
                    </div>

                    {{-- Sort Filter --}}
                    <form action="{{ route('properties.index') }}" method="GET" class="flex items-center gap-2">
                        @foreach (request()->except('sort') as $k => $v)
                            @if (is_array($v))
                                @foreach ($v as $item)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="view" :value="viewMode">

                        <label class="text-xs font-bold text-slate-500 shrink-0">Sort:</label>
                        <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 focus:border-indigo-600 focus:outline-hidden">
                            <option value="">Popular</option>
                            <option value="rent_asc" {{ request('sort') === 'rent_asc' ? 'selected' : '' }}>Rent: Low</option>
                            <option value="rent_desc" {{ request('sort') === 'rent_desc' ? 'selected' : '' }}>Rent: High</option>
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Rated</option>
                        </select>
                    </form>
                </div>
            </div>

            {{-- INTERACTIVE MAP VIEW CONTAINER --}}
            <div x-show="viewMode === 'map'" x-cloak class="space-y-4">
                <div class="relative rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100">
                    <div id="staysMap" class="h-[460px] w-full z-10"></div>

                    {{-- Map Overlay Instruction Badge --}}
                    <div class="absolute bottom-4 left-4 z-20 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-xl text-[11px] font-bold text-slate-700 shadow-md border border-slate-200">
                        📍 Click on any price marker to inspect accommodation preview
                    </div>
                </div>
            </div>

            {{-- Properties Grid (Displayed in both grid and below map) --}}
            @if ($properties->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($properties as $property)
                        <x-property-card :property="$property" />
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="mt-8 pt-6 border-t border-slate-100">
                    {{ $properties->links() }}
                </div>
            @else
                {{-- No Results Empty State --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-xs">
                    <div class="h-16 w-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl mx-auto mb-4">
                        🔍
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">No accommodations match your filters</h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                        Try relaxing your budget, removing specific amenities, or selecting "Any" for room sharing to view more options.
                    </p>
                    <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                        Clear All Filters
                    </a>
                </div>
            @endif

        </main>
    </div>

    {{-- Mobile Filters Modal --}}
    <div x-show="mobileFiltersOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
        <div x-show="mobileFiltersOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs"></div>
        <div class="fixed inset-0 flex justify-end">
            <div x-show="mobileFiltersOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-full max-w-xs bg-white h-full p-6 overflow-y-auto shadow-2xl flex flex-col justify-between">
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <h3 class="text-base font-extrabold text-slate-900">Filter Stays</h3>
                        <button @click="mobileFiltersOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Form for mobile --}}
                    <form action="{{ route('properties.index') }}" method="GET" class="space-y-6">
                        @if (request('query'))
                            <input type="hidden" name="query" value="{{ request('query') }}">
                        @endif

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">City</label>
                            <select name="city" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">
                                <option value="">All Cities</option>
                                @foreach ($allCities as $cityName)
                                    <option value="{{ $cityName }}" {{ request('city') === $cityName ? 'selected' : '' }}>
                                        {{ $cityName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Type</label>
                            <div class="space-y-1.5 text-xs">
                                @foreach ($types as $key => $label)
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="type" value="{{ $key }}" {{ request('type') === $key ? 'checked' : '' }} class="text-indigo-600">
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Gender</label>
                            <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 rounded-xl text-xs font-bold text-center">
                                <label><input type="radio" name="gender" value="any" {{ request('gender', 'any') === 'any' ? 'checked' : '' }} class="sr-only peer"><div class="py-1 rounded-lg peer-checked:bg-white peer-checked:text-indigo-700">Any</div></label>
                                <label><input type="radio" name="gender" value="male" {{ request('gender') === 'male' ? 'checked' : '' }} class="sr-only peer"><div class="py-1 rounded-lg peer-checked:bg-white peer-checked:text-indigo-700">Boys</div></label>
                                <label><input type="radio" name="gender" value="female" {{ request('gender') === 'female' ? 'checked' : '' }} class="sr-only peer"><div class="py-1 rounded-lg peer-checked:bg-white peer-checked:text-indigo-700">Girls</div></label>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="w-full rounded-2xl bg-indigo-600 py-3 text-xs font-bold text-white shadow-md">
                                Apply & View ({{ $properties->total() }})
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        function staysExplorer() {
            return {
                mobileFiltersOpen: false,
                viewMode: '{{ request('view') === 'map' ? 'map' : 'grid' }}',
                mapInitialized: false,
                init() {
                    if (this.viewMode === 'map') {
                        this.initMap();
                    }
                },
                initMap() {
                    if (this.mapInitialized || typeof L === 'undefined') return;
                    this.$nextTick(() => {
                        setTimeout(() => {
                            const properties = @json($mapProperties);
                            const mapEl = document.getElementById('staysMap');
                            if (!mapEl) return;

                            let defaultLat = 18.5204;
                            let defaultLng = 73.8567;
                            if (properties.length > 0) {
                                defaultLat = properties[0].lat;
                                defaultLng = properties[0].lng;
                            }

                            const map = L.map('staysMap').setView([defaultLat, defaultLng], 12);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(map);

                            const markersGroup = L.featureGroup().addTo(map);

                            properties.forEach(prop => {
                                const icon = L.divIcon({
                                    className: 'custom-map-pill',
                                    html: `<div class='bg-indigo-600 text-white font-extrabold text-[11px] px-2.5 py-1 rounded-full shadow-lg border-2 border-white hover:bg-slate-900 transition cursor-pointer flex items-center justify-center whitespace-nowrap'>${prop.price}/mo</div>`,
                                    iconSize: [75, 30],
                                    iconAnchor: [37, 15]
                                });

                                const popupHtml = `
                                    <div class='p-1 max-w-[210px]'>
                                        <img src='${prop.image}' alt='${prop.title}' class='h-24 w-full object-cover rounded-xl mb-2'>
                                        <span class='text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded'>${prop.type} &bull; ${prop.gender}</span>
                                        <h4 class='font-black text-xs text-slate-900 mt-1 line-clamp-1'>${prop.title}</h4>
                                        <div class='flex items-center justify-between text-[11px] font-bold text-slate-800 mt-1'>
                                            <span class='text-indigo-600'>${prop.rent_display}</span>
                                            <span class='${prop.available_beds > 0 ? 'text-emerald-600' : 'text-rose-500'}'>${prop.available_beds > 0 ? prop.available_beds + ' vacant' : 'Full'}</span>
                                        </div>
                                        <a href='${prop.url}' target='_blank' class='block w-full text-center bg-indigo-600 text-white text-[11px] font-bold py-1.5 rounded-lg mt-2 hover:bg-indigo-700 transition'>View Stay &rarr;</a>
                                    </div>
                                `;

                                const marker = L.marker([prop.lat, prop.lng], { icon: icon }).bindPopup(popupHtml);
                                markersGroup.addLayer(marker);
                            });

                            if (markersGroup.getLayers().length > 0) {
                                map.fitBounds(markersGroup.getBounds().pad(0.2));
                            }

                            this.mapInitialized = true;
                            window.stayFinderMap = map;
                        }, 200);
                    });
                }
            };
        }

        function detectStaysNearMe(btn) {
            const label = document.getElementById('stays-near-me-text');
            if (!('geolocation' in navigator)) {
                alert('Geolocation is not supported by your browser.');
                return;
            }
            if (label) label.textContent = 'Detecting...';
            btn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    document.getElementById('stays-geo-lat').value = pos.coords.latitude;
                    document.getElementById('stays-geo-lng').value = pos.coords.longitude;
                    const citySelect = document.querySelector('select[name="city"]');
                    if (citySelect) citySelect.value = '';

                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&zoom=14', { headers: { 'Accept': 'application/json' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            const place = (data.address && (data.address.suburb || data.address.city || data.address.town)) || '';
                            const searchInput = document.getElementById('stays-search-input');
                            if (place && searchInput && !searchInput.value) {
                                searchInput.value = place;
                            }
                            btn.closest('form').submit();
                        })
                        .catch(function() {
                            btn.closest('form').submit();
                        });
                },
                function(err) {
                    if (label) label.textContent = 'Near Me';
                    btn.disabled = false;
                    alert('Location permission was denied or is unavailable.');
                },
                { timeout: 7000 }
            );
        }
    </script>
@endpush
