@extends('layouts.app', ['title' => $property->title . ' - StayFinder'])

@push('styles')
    @if ($property->image_360)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    @endif
@endpush

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="{
    activeImage: '{{ $property->primary_image_url }}',
    contactModalOpen: false,
    contactAction: '',
    isSaved: {{ Auth::check() && $property->isSavedBy(Auth::user()) ? 'true' : 'false' }},
    savingFavorite: false,
    inquirySuccess: false,
    inquiryLoading: false,
    reportModalOpen: false,
    reportLoading: false,
    reportSuccess: false,

    toggleFavorite() {
        if (! {{ Auth::check() ? 'true' : 'false' }}) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        this.savingFavorite = true;
        fetch('{{ route('properties.favorite', $property->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            this.isSaved = data.is_saved;
            this.savingFavorite = false;
        })
        .catch(err => { this.savingFavorite = false; });
    }
}">

    {{-- Breadcrumbs & Back Link --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 text-xs">
        <nav class="flex items-center gap-2 text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Home</a>
            <span>/</span>
            <a href="{{ route('properties.index', ['city' => $property->city]) }}" class="hover:text-indigo-600 transition">{{ $property->city }}</a>
            <span>/</span>
            <span class="text-slate-900 font-bold truncate max-w-xs">{{ $property->title }}</span>
        </nav>

        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('properties.index') }}"
            class="inline-flex items-center gap-1 font-bold text-indigo-600 hover:text-indigo-700">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            <span>Back to search results</span>
        </a>
    </div>

    {{-- Property Title & Badges Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="rounded-lg bg-indigo-50 border border-indigo-200/60 px-2.5 py-0.5 text-xs font-extrabold text-indigo-700">
                    {{ $property->type_label }}
                </span>
                <span class="rounded-lg bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">
                    {{ $property->gender_label }}
                </span>

                @if ($property->is_verified)
                    <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 text-white px-2.5 py-0.5 text-xs font-bold shadow-xs">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        Verified Student Stay ✓
                    </span>
                @endif

                @if ($property->is_featured)
                    <span class="rounded-lg bg-amber-500 text-white px-2.5 py-0.5 text-xs font-bold">
                        ⭐ Featured
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900">
                {{ $property->title }}
            </h1>

            <p class="mt-1.5 flex items-center gap-1.5 text-xs sm:text-sm text-slate-500">
                <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ $property->address }}, {{ $property->locality }}, {{ $property->city }}</span>
            </p>
        </div>

        {{-- Top Right Share & Favorite --}}
        <div class="flex items-center gap-3 shrink-0">
            <button type="button" @click="navigator.clipboard.writeText(window.location.href); alert('Listing link copied to clipboard!');"
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                Share
            </button>
            <button type="button" @click="toggleFavorite()"
                class="rounded-xl border px-3 py-2 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs"
                :class="isSaved ? 'bg-rose-50 border-rose-200 text-rose-600' : 'bg-white border-slate-200 text-slate-700 hover:text-rose-600 hover:bg-rose-50'">
                <svg class="h-4 w-4" :fill="isSaved ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span x-text="isSaved ? 'Saved Stay ✓' : 'Save Stay'"></span>
            </button>
        </div>
    </div>

    {{-- =========================================================
        IMAGE GALLERY
    ========================================================== --}}
    <div class="mb-10 space-y-3">
        {{-- Large Feature Image --}}
        <div class="relative h-80 sm:h-[480px] w-full rounded-3xl bg-slate-100 overflow-hidden shadow-md">
            <img :src="activeImage" alt="{{ $property->title }}" class="w-full h-full object-cover transition-all duration-300">
        </div>

        {{-- Thumbnails --}}
        @if ($property->images->count() > 1)
            <div class="flex items-center gap-3 overflow-x-auto pb-2">
                @foreach ($property->images as $img)
                    <button type="button" @click="activeImage = '{{ $img->url }}'"
                        :class="activeImage === '{{ $img->url }}' ? 'ring-3 ring-indigo-600 ring-offset-2' : 'opacity-70 hover:opacity-100'"
                        class="relative h-20 w-28 rounded-2xl overflow-hidden shrink-0 transition">
                        <img src="{{ $img->url }}" alt="{{ $img->caption ?? 'Property Thumbnail' }}" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- =========================================================
        INTERACTIVE 360° VIRTUAL TOUR (Pannellum Viewer)
    ========================================================== --}}
    @if ($property->image_360)
        <div class="mb-10 rounded-3xl border border-indigo-200 bg-white p-4 sm:p-6 shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 pb-3 border-b border-indigo-50">
                <div class="flex items-center gap-2">
                    <span class="h-8 w-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-sm shadow-xs">🔄</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                            Interactive 360° Virtual Room Tour
                            <span class="text-[10px] font-extrabold uppercase tracking-wider bg-indigo-600 text-white px-2.5 py-0.5 rounded-full">Live 360°</span>
                        </h3>
                        <p class="text-xs text-slate-500">Drag with mouse or swipe with finger on mobile to look 360° around this room!</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100">
                    <span>👆 360° Touch & Pan Enabled</span>
                </div>
            </div>

            <div id="panorama-360-viewer" class="w-full h-80 sm:h-[450px] rounded-2xl overflow-hidden shadow-inner bg-slate-900"></div>
        </div>
    @endif

    {{-- Main Content & Sidebar Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left Details Column (8 Cols) --}}
        <div class="lg:col-span-8 space-y-10">
            
            {{-- Safety Critical Notice --}}
            <div class="rounded-2xl border border-amber-300 bg-amber-50/80 p-5 text-amber-900 shadow-xs">
                <div class="flex items-start gap-3">
                    <svg class="h-6 w-6 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-bold text-amber-950">StayFinder Student Safety Shield</h4>
                        <p class="text-xs text-amber-900 mt-0.5 leading-relaxed">
                            <strong class="text-amber-950">Never transfer advance money or gate deposit</strong> before inspecting this accommodation in person and verifying the room, washroom, and food hygiene yourself.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Quick Features Pill Bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-3xl border border-slate-200 bg-white shadow-xs text-center">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Stay Type</span>
                    <p class="text-sm font-bold text-slate-900">{{ $property->type_label }}</p>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Gender</span>
                    <p class="text-sm font-bold text-slate-900">{{ $property->gender_label }}</p>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Meals</span>
                    <p class="text-sm font-bold text-slate-900">{{ $property->food_included ? 'Included' : 'Self/Cook' }}</p>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Gate Curfew</span>
                    <p class="text-sm font-bold text-slate-900">{{ $property->gate_closing_time ?? 'None' }}</p>
                </div>
            </div>

            {{-- Description --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                <h3 class="text-lg font-bold text-slate-900 mb-3">About This Accommodation</h3>
                <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600">
                    {{ $property->description }}
                </div>
            </div>

            {{-- Available Room Types & Sharing Options --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Room Sharing Options & Pricing</h3>
                    <span class="text-xs font-semibold text-slate-500">{{ $property->rooms->count() }} Room Variant(s)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse ($property->rooms as $room)
                        <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 hover:border-indigo-400 transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-100/70 px-2 py-0.5 rounded-md">
                                        {{ $room->room_type_label }}
                                    </span>
                                    <span class="text-xs font-semibold {{ $room->available_capacity > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $room->available_capacity > 0 ? $room->available_capacity . ' bed(s) vacant' : 'Full' }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 mb-2">{{ $room->title }}</h4>

                                <div class="space-y-1 text-xs text-slate-600 mb-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="{{ $room->has_attached_bathroom ? 'text-emerald-500' : 'text-slate-300' }}">✓</span>
                                        <span>Attached Washroom</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="{{ $room->has_ac ? 'text-emerald-500' : 'text-slate-300' }}">✓</span>
                                        <span>Air Conditioned (AC)</span>
                                    </div>
                                    @if ($room->has_balcony)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-emerald-500">✓</span>
                                            <span>Private Balcony</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/80 flex items-baseline justify-between">
                                <div>
                                    <span class="text-lg font-black text-slate-900">₹{{ number_format($room->monthly_rent) }}</span>
                                    <span class="text-xs text-slate-500">/ mo</span>
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    ₹{{ number_format($room->security_deposit) }} Deposit
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 col-span-2">Contact provider for specific room occupancy configurations.</p>
                    @endforelse
                </div>
            </div>

            {{-- Amenities Grid --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Amenities & Facilities Included</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($property->amenities as $amenity)
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/60">
                            <div class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs shrink-0">
                                ✓
                            </div>
                            <span class="text-xs font-semibold text-slate-800">{{ $amenity->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Food Details & Menu --}}
            @if ($property->food_details)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                            🍲
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Food & Meals Schedule</h3>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $property->food_details }}
                    </p>
                </div>
            @endif

            {{-- Nearby Colleges & Educational Hubs --}}
            @if (! empty($property->nearby_colleges))
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Distance From Nearby Colleges</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($property->nearby_colleges as $college)
                            <div class="flex items-center justify-between p-3.5 rounded-2xl border border-indigo-100 bg-indigo-50/40">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-indigo-600 font-bold">🎓</span>
                                    <span class="text-xs font-bold text-slate-900">{{ $college['name'] ?? '' }}</span>
                                </div>
                                <span class="text-xs font-extrabold text-indigo-700 bg-white px-2.5 py-1 rounded-lg shadow-2xs">
                                    {{ $college['distance_km'] ?? '?' }} km
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- House Rules --}}
            @if (! empty($property->rules))
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">House Rules & Terms</h3>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-600 list-disc list-inside">
                        @foreach ($property->rules as $rule)
                            <li>{{ $rule }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Location & Map Container (Provider-Agnostic Leaflet/OSM container) --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Location & Map</h3>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $property->latitude }},{{ $property->longitude }}"
                        target="_blank" rel="noopener noreferrer"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                        <span>Open in Google Maps</span>
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                </div>

                <p class="text-xs text-slate-500 mb-4">
                    📍 {{ $property->address }}, {{ $property->locality }}, {{ $property->city }}
                    @if ($property->latitude && $property->longitude)
                        <span class="text-slate-400">({{ $property->latitude }}, {{ $property->longitude }})</span>
                    @endif
                </p>

                {{-- Map abstraction viewport --}}
                <div class="h-64 rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden relative flex items-center justify-center text-center p-6">
                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ ($property->longitude ?? 73.9143) - 0.01 }}%2C{{ ($property->latitude ?? 18.5679) - 0.01 }}%2C{{ ($property->longitude ?? 73.9143) + 0.01 }}%2C{{ ($property->latitude ?? 18.5679) + 0.01 }}&amp;layer=mapnik&amp;marker={{ $property->latitude ?? 18.5679 }}%2C{{ $property->longitude ?? 73.9143 }}">
                    </iframe>
                </div>
            </div>

            {{-- =========================================================
                STUDENT REVIEWS & RATINGS SECTION
            ========================================================== --}}
            <div id="reviews" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Student Reviews & Ratings</h3>
                        <p class="text-xs text-slate-500">Verified student reviews and accommodation feedback.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black text-amber-500">★ {{ number_format($property->rating, 1) }}</span>
                        <span class="text-xs text-slate-400">({{ $property->review_count }} reviews)</span>
                    </div>
                </div>

                {{-- Sub-Ratings Criteria Breakdown --}}
                @php
                    $approvedReviews = $property->reviews->where('is_approved', true);
                    $avgClean = $approvedReviews->avg('cleanliness_rating') ?: 4.5;
                    $avgFood = $approvedReviews->avg('food_rating') ?: 4.2;
                    $avgWifi = $approvedReviews->avg('wifi_rating') ?: 4.8;
                    $avgSafety = $approvedReviews->avg('safety_rating') ?: 4.9;
                    $avgBehavior = $approvedReviews->avg('behavior_rating') ?: 4.6;
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block">Cleanliness</span>
                        <span class="text-sm font-black text-slate-900">★ {{ number_format($avgClean, 1) }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block">Food / Mess</span>
                        <span class="text-sm font-black text-slate-900">★ {{ number_format($avgFood, 1) }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block">WiFi Speed</span>
                        <span class="text-sm font-black text-slate-900">★ {{ number_format($avgWifi, 1) }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block">Safety & Rules</span>
                        <span class="text-sm font-black text-slate-900">★ {{ number_format($avgSafety, 1) }}</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <span class="text-[11px] font-bold text-slate-500 block">Warden Behavior</span>
                        <span class="text-sm font-black text-slate-900">★ {{ number_format($avgBehavior, 1) }}</span>
                    </div>
                </div>

                {{-- Write a Review Form --}}
                @auth
                    @if (Auth::id() !== $property->user_id)
                        <div class="p-5 rounded-2xl border border-indigo-100 bg-indigo-50/40 space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-900">Write a Student Review</h4>
                            <form action="{{ route('reviews.store', $property->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Overall *</label>
                                        <select name="rating" required class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-bold text-amber-600 bg-white">
                                            <option value="5">5 ★★★★★</option>
                                            <option value="4">4 ★★★★</option>
                                            <option value="3">3 ★★★</option>
                                            <option value="2">2 ★★</option>
                                            <option value="1">1 ★</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Cleanliness</label>
                                        <select name="cleanliness_rating" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs bg-white">
                                            <option value="5">5/5 Excellent</option>
                                            <option value="4">4/5 Good</option>
                                            <option value="3">3/5 Average</option>
                                            <option value="2">2/5 Poor</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Food / Mess</label>
                                        <select name="food_rating" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs bg-white">
                                            <option value="5">5/5 Tasty</option>
                                            <option value="4">4/5 Decent</option>
                                            <option value="3">3/5 Average</option>
                                            <option value="2">2/5 Bad</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">WiFi</label>
                                        <select name="wifi_rating" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs bg-white">
                                            <option value="5">5/5 Fast</option>
                                            <option value="4">4/5 Good</option>
                                            <option value="3">3/5 Okay</option>
                                            <option value="2">2/5 Slow</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Safety</label>
                                        <select name="safety_rating" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs bg-white">
                                            <option value="5">5/5 Very Safe</option>
                                            <option value="4">4/5 Safe</option>
                                            <option value="3">3/5 Moderate</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Behavior</label>
                                        <select name="behavior_rating" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs bg-white">
                                            <option value="5">5/5 Polite</option>
                                            <option value="4">4/5 Helpful</option>
                                            <option value="3">3/5 Strict</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <textarea name="review" rows="3" required placeholder="Share your experience regarding food, electricity/power backup, security, and owner behavior..."
                                        class="w-full rounded-xl border border-slate-300 p-3 text-xs bg-white focus:border-indigo-600 focus:outline-hidden"></textarea>
                                </div>

                                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-xs">
                                    Publish Student Review
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 text-center text-xs text-slate-500">
                        <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">Sign In</a> as a student to leave a review for this accommodation.
                    </div>
                @endauth

                {{-- Reviews List --}}
                <div class="space-y-4 divide-y divide-slate-100">
                    @forelse ($approvedReviews as $rev)
                        <div class="pt-4 first:pt-0 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                        {{ substr($rev->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900">{{ $rev->user->name }}</span>
                                        @if ($rev->user->studentProfile?->college_name)
                                            <span class="text-[11px] text-slate-400 block">{{ $rev->user->studentProfile->college_name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-extrabold text-amber-500">★ {{ $rev->rating }}.0</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $rev->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed pl-11">
                                {{ $rev->review }}
                            </p>

                            {{-- Provider Reply --}}
                            @if ($rev->provider_reply)
                                <div class="ml-11 mt-2 p-3 rounded-xl bg-slate-100 border-l-4 border-indigo-600 text-xs space-y-1">
                                    <span class="font-bold text-indigo-900 block text-[11px]">💬 Official Response from Owner:</span>
                                    <p class="text-slate-600">{{ $rev->provider_reply }}</p>
                                </div>
                            @elseif (Auth::id() === $property->user_id)
                                {{-- Owner can reply --}}
                                <div class="ml-11 mt-2" x-data="{ replying: false }">
                                    <button type="button" @click="replying = !replying" class="text-xs font-bold text-indigo-600 hover:underline">
                                        Reply to this review
                                    </button>
                                    <form x-show="replying" action="{{ route('reviews.reply', $rev->id) }}" method="POST" class="mt-2 space-y-2">
                                        @csrf
                                        <textarea name="provider_reply" rows="2" required placeholder="Write your official response..."
                                            class="w-full rounded-xl border border-slate-300 p-2 text-xs"></textarea>
                                        <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-1 text-[11px] font-bold text-white hover:bg-indigo-700">Submit Reply</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-400">
                            No student reviews yet. Be the first student to review this stay!
                        </div>
                    @endforelse
                </div>

            </div>

        </div>

        {{-- Right Floating Contact & Provider Card (4 Cols) --}}
        <aside class="lg:col-span-4 space-y-6 sticky top-24">
            
            {{-- Rent & Contact Actions Card --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xl shadow-slate-100">
                
                {{-- Rent display --}}
                <div class="mb-6 pb-6 border-b border-slate-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Monthly Rent</span>
                    <div class="text-3xl font-black text-slate-900 mt-1">
                        {{ $property->rent_display }}
                    </div>
                    <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                        <span>Deposit: <strong>₹{{ number_format($property->security_deposit) }}</strong></span>
                        <span>Notice: <strong>{{ $property->notice_period_days }} days</strong></span>
                    </div>
                </div>

                {{-- CTAs --}}
                <div class="space-y-3">
                    {{-- In-App Chat CTA --}}
                    @auth
                        <form method="POST" action="{{ route('chat.start', $property->id) }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 py-3.5 px-4 text-sm font-bold text-white shadow-md shadow-indigo-200 hover:bg-indigo-700 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Chat with Provider (Free)
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-full flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 py-3.5 px-4 text-sm font-bold text-white shadow-md shadow-indigo-200 hover:bg-indigo-700 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Chat with Provider (Free)
                        </a>
                    @endauth

                    {{-- Send Booking Inquiry Form --}}
                    <button type="button" @click="contactModalOpen = true; contactAction = 'inquiry'"
                        class="w-full flex items-center justify-center gap-2 rounded-2xl border border-indigo-200 bg-indigo-50/50 py-2.5 px-4 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition">
                        <span>📬 Send Booking Inquiry Form</span>
                    </button>

                    {{-- Call Provider --}}
                    @php
                        $providerPhone = $property->user->providerProfile?->phone ?? $property->user->phone ?? '+919876543210';
                        $whatsappNumber = $property->user->providerProfile?->whatsapp_number ?? $providerPhone;
                    @endphp

                    <div class="grid grid-cols-2 gap-2">
                        <a href="tel:{{ $providerPhone }}"
                            class="flex items-center justify-center gap-1.5 rounded-2xl border border-slate-300 py-3 px-3 text-xs font-bold text-slate-800 hover:bg-slate-50 transition shadow-2xs">
                            <svg class="h-4 w-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Call Owner
                        </a>

                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}?text={{ urlencode('Hi! I saw your accommodation ' . $property->title . ' on StayFinder. Is a bed currently available?') }}"
                            target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center gap-1.5 rounded-2xl border border-emerald-300 bg-emerald-50/60 py-3 px-3 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition shadow-2xs">
                            <span class="text-emerald-600 font-bold">💬</span>
                            WhatsApp
                        </a>
                    </div>

                    {{-- Get Directions --}}
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $property->latitude }},{{ $property->longitude }}"
                        target="_blank" rel="noopener noreferrer"
                        class="w-full flex items-center justify-center gap-2 rounded-2xl border border-slate-200 py-2.5 px-4 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                        Get Directions
                    </a>
                </div>

                {{-- Safe student guarantee note --}}
                <p class="mt-5 text-center text-[11px] text-slate-400">
                    🔒 Zero student booking fee &bull; Direct owner contact
                </p>
            </div>

            {{-- Provider Information Card --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-3">Listed By Provider</span>
                
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-black text-base flex items-center justify-center shadow-xs">
                        {{ strtoupper(substr($property->user->providerProfile?->business_name ?? $property->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">
                            {{ $property->user->providerProfile?->business_name ?? $property->user->name }}
                        </h4>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <span>Owner: {{ $property->user->providerProfile?->owner_name ?? $property->user->name }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 text-xs text-slate-600 pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <span>Identity Verification</span>
                        <span class="font-bold {{ $property->user->providerProfile?->is_verified ? 'text-emerald-600' : 'text-slate-500' }}">
                            {{ $property->user->providerProfile?->is_verified ? 'Verified ID ✓' : 'Under Review' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Response Rate</span>
                        <span class="font-bold text-slate-900">{{ $property->user->providerProfile?->response_rate ?? 98 }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Avg. Response Time</span>
                        <span class="font-bold text-slate-900">{{ $property->user->providerProfile?->avg_response_time ?? 'Within 30 mins' }}</span>
                    </div>
                </div>
            </div>

            {{-- Report Listing Action --}}
            <div class="text-center pt-2">
                <button type="button" @click="reportModalOpen = true"
                    class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline inline-flex items-center gap-1.5 transition">
                    <span>🚩 Report Listing or Scam Alert</span>
                </button>
            </div>

        </aside>

    </div>

    {{-- Contact / Inquiry Modal --}}
    <div x-show="contactModalOpen" class="relative z-50" role="dialog" aria-modal="true">
        <div x-show="contactModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div x-show="contactModalOpen" x-transition @click.outside="contactModalOpen = false" class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Send Inquiry to Provider</h3>
                    <button @click="contactModalOpen = false; inquirySuccess = false;" class="p-1 rounded-lg text-slate-400 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div x-show="inquirySuccess" class="text-center py-6 space-y-4">
                    <div class="h-16 w-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-3xl mx-auto">
                        ✓
                    </div>
                    <h4 class="text-lg font-black text-slate-900">Inquiry Delivered!</h4>
                    <p class="text-xs text-slate-500 max-w-xs mx-auto">
                        Your details have been sent to the accommodation provider. They will contact you shortly via phone or WhatsApp.
                    </p>
                    <button type="button" @click="contactModalOpen = false; inquirySuccess = false;" class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white">
                        Done
                    </button>
                </div>

                <div x-show="!inquirySuccess">
                    <p class="text-xs text-slate-500 mb-6">
                        Direct inquiry for <strong class="text-slate-900">{{ $property->title }}</strong>. The provider will be notified and will reply via phone or chat.
                    </p>

                    <form @submit.prevent="
                        inquiryLoading = true;
                        fetch('{{ route('properties.inquire', $property->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                student_name: $el.student_name.value,
                                student_phone: $el.student_phone.value,
                                target_move_in_date: $el.target_move_in_date.value,
                                preferred_room_type: $el.preferred_room_type.value,
                                message: $el.message.value
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            inquiryLoading = false;
                            if (data.success) {
                                inquirySuccess = true;
                            } else {
                                alert(data.message || 'Please check your inputs.');
                            }
                        })
                        .catch(err => {
                            inquiryLoading = false;
                            alert('Failed to send inquiry. Please try again or call the provider directly.');
                        });
                    " class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Your Name *</label>
                            <input type="text" name="student_name" value="{{ Auth::user()->name ?? '' }}" required placeholder="Your full name"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs focus:border-indigo-600 focus:outline-hidden">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Phone / WhatsApp Number *</label>
                            <input type="tel" name="student_phone" value="{{ Auth::user()->phone ?? '+91 ' }}" required placeholder="+91 98765 43210"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs focus:border-indigo-600 focus:outline-hidden">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Target Move-in *</label>
                                <input type="date" name="target_move_in_date" required value="{{ date('Y-m-d', strtotime('+7 days')) }}"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-indigo-600 focus:outline-hidden">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Room Preference</label>
                                <select name="preferred_room_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-indigo-600 focus:outline-hidden">
                                    <option value="any">Any Available</option>
                                    <option value="single">Single (Private)</option>
                                    <option value="double">Double Sharing</option>
                                    <option value="triple">Triple Sharing</option>
                                    <option value="four_plus">4+ Sharing</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Message to Provider</label>
                            <textarea name="message" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-xs focus:border-indigo-600 focus:outline-hidden"
                                placeholder="Hello, I am interested in this stay. Is a bed currently available?"></textarea>
                        </div>

                        <button type="submit" :disabled="inquiryLoading"
                            class="w-full rounded-xl bg-indigo-600 py-3 text-xs font-bold text-white shadow-md hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                            <span x-text="inquiryLoading ? 'Sending Inquiry...' : 'Submit Direct Inquiry'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Scam & Safety Report Modal --}}
    <div x-show="reportModalOpen" class="relative z-50" role="dialog" aria-modal="true" x-cloak>
        <div x-show="reportModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20 flex items-center justify-center">
            <div x-show="reportModalOpen" @click.outside="reportModalOpen = false"
                class="w-full max-w-md rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-1.5">
                        <span>🚩</span> Report Listing Concern
                    </h3>
                    <button @click="reportModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <div x-show="reportSuccess" class="py-6 text-center space-y-3">
                    <div class="h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-xl font-bold">✓</div>
                    <h4 class="font-bold text-slate-900">Report Submitted</h4>
                    <p class="text-xs text-slate-500">Our trust & safety team will investigate this listing immediately.</p>
                    <button @click="reportModalOpen = false; reportSuccess = false" class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700">Done</button>
                </div>

                <form x-show="!reportSuccess" @submit.prevent="
                    reportLoading = true;
                    fetch('{{ route('properties.report', $property->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            reason: $el.reason.value,
                            description: $el.description.value,
                            reporter_name: $el.reporter_name.value,
                            reporter_phone: $el.reporter_phone.value,
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        reportLoading = false;
                        if (data.success) {
                            reportSuccess = true;
                        } else {
                            alert(data.message || 'Error submitting report.');
                        }
                    })
                    .catch(err => {
                        reportLoading = false;
                        alert('Could not submit report.');
                    });
                " class="space-y-4 mt-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Reason *</label>
                        <select name="reason" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800">
                            <option value="advance_payment_scam">Asking advance money / deposit before visit</option>
                            <option value="fake_photos">Fake or misleading photographs</option>
                            <option value="incorrect_pricing">Incorrect rent / hidden charges</option>
                            <option value="unsafe_environment">Unsafe environment or caretaker harassment</option>
                            <option value="already_full">Accommodation is already fully occupied</option>
                            <option value="other">Other Concern</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Specific Details *</label>
                        <textarea name="description" rows="3" required placeholder="Describe what occurred or why this listing seems suspicious..."
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Your Name</label>
                            <input type="text" name="reporter_name" value="{{ Auth::user()->name ?? '' }}" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Your Phone</label>
                            <input type="tel" name="reporter_phone" value="{{ Auth::user()->phone ?? '' }}" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs">
                        </div>
                    </div>

                    <button type="submit" :disabled="reportLoading"
                        class="w-full rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white hover:bg-rose-700 transition">
                        <span x-text="reportLoading ? 'Submitting Report...' : 'Submit Scam / Safety Alert'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    @if ($property->image_360)
        <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('panorama-360-viewer');
                if (el && typeof pannellum !== 'undefined') {
                    pannellum.viewer('panorama-360-viewer', {
                        type: 'equirectangular',
                        panorama: '{{ $property->image_360_url }}',
                        autoLoad: true,
                        autoRotate: -2,
                        showZoomCtrl: true,
                        compass: true
                    });
                }
            });
        </script>
    @endif
@endpush
