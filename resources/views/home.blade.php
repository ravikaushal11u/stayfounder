@extends('layouts.app')

@section('content')
<div class="space-y-16 lg:space-y-24 pb-16">
    
    {{-- =========================================================
        HERO SECTION & STUDENT SEARCH INTERFACE (Human & Grounded)
    ========================================================== --}}
    <section class="relative bg-slate-900 text-white pt-12 pb-20 sm:pt-16 sm:pb-28 overflow-hidden border-b border-slate-800">
        {{-- Subtle Grid Pattern (Authentic, not neon AI) --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-40 pointer-events-none"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10">
                {{-- Clean Authentic Trust Tag (No AI sparkles) --}}
                <div class="inline-flex items-center gap-2 rounded-full bg-slate-800 border border-slate-700 px-4 py-1.5 text-xs font-semibold text-slate-300 mb-6 shadow-xs">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>Verified Student Stays Across India</span>
                    <span class="text-slate-500">•</span>
                    <span class="text-white font-bold">Zero Brokerage</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-tight">
                    Find a place that feels like home.
                </h1>

                <p class="mt-4 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto font-normal leading-relaxed">
                    Verified student PGs, hostels, and rooms directly near your college or coaching. Zero broker commission, 360° virtual tours, and student rents starting from ₹1,000/month.
                </p>
            </div>

            {{-- SEARCH ENGINE CARD --}}
            <div id="search-section" class="max-w-4xl mx-auto rounded-3xl border border-slate-700/60 bg-white p-4 sm:p-6 text-slate-800 shadow-2xl shadow-slate-950/60">
                <form action="{{ route('properties.index') }}" method="GET" class="space-y-4">
                    
                    {{-- Top Row: City/College & Near Me --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        
                        {{-- Location Input --}}
                        <div class="md:col-span-8 relative">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Where is your college, coaching or campus?
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <input type="text" name="query" id="hero-search-query" value="{{ request('query') }}"
                                    placeholder="Search city, area, college (e.g. Pune, Hazaribagh, Kota, DU, Sindur)"
                                    class="w-full rounded-xl border border-slate-300 pl-11 pr-4 py-3 text-sm font-semibold placeholder-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-hidden transition">
                            </div>
                        </div>

                        {{-- Geolocation "Near Me" Button --}}
                        <div class="md:col-span-4 flex flex-col justify-end">
                            <label class="hidden md:block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Current Location Access
                            </label>
                            <input type="hidden" name="lat" id="geo-lat" value="">
                            <input type="hidden" name="lng" id="geo-lng" value="">
                            <input type="hidden" name="distance" value="15">
                            <button type="button" id="home-geo-btn" onclick="detectHomeLocation(this)"
                                class="w-full flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100 hover:border-slate-400 transition shadow-xs">
                                <span>📍</span>
                                <span id="home-geo-label">Use Current Location</span>
                            </button>
                        </div>
                    </div>

                    {{-- Bottom Filters Row: Stay Type, Budget, Gender, Search --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2 border-t border-slate-100">
                        
                        {{-- Accommodation Type --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Stay Type</label>
                            <select name="type" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-slate-900 focus:outline-hidden">
                                <option value="">All Types (PG, Hostel, Room)</option>
                                <option value="pg">Paying Guest (PG)</option>
                                <option value="hostel">Student Hostel</option>
                                <option value="room">Private Single Room</option>
                                <option value="flat">Student Flat / 1BHK</option>
                                <option value="lodge">Lodge / Temporary</option>
                            </select>
                        </div>

                        {{-- Budget Range --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Budget</label>
                            <select name="budget" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-slate-900 focus:outline-hidden">
                                <option value="">Any Budget</option>
                                <option value="1500">Starting ₹1,000 - ₹1,500 / mo</option>
                                <option value="3000">Under ₹3,000 / mo</option>
                                <option value="5000">Under ₹5,000 / mo</option>
                                <option value="8000">Under ₹8,000 / mo</option>
                                <option value="12000">Under ₹12,000 / mo</option>
                                <option value="20000">Under ₹20,000 / mo</option>
                            </select>
                        </div>

                        {{-- Gender Preference --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Gender</label>
                            <select name="gender" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-slate-900 focus:outline-hidden">
                                <option value="">Any Gender</option>
                                <option value="male">Boys Only</option>
                                <option value="female">Girls Only</option>
                                <option value="any">Co-ed / Any</option>
                            </select>
                        </div>

                        {{-- Search CTA --}}
                        <div class="flex items-end">
                            <button type="submit" class="w-full rounded-xl bg-slate-900 py-2.5 px-4 text-xs font-bold text-white shadow-md hover:bg-slate-800 focus:outline-hidden transition flex items-center justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Search Stays
                            </button>
                        </div>
                    </div>

                    {{-- Quick Campus Chips --}}
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs">
                        <span class="text-slate-400 font-bold text-[11px] uppercase tracking-wider">🎓 Popular Campuses:</span>
                        <a href="{{ route('properties.index', ['query' => 'Symbiosis', 'city' => 'Pune']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition">Symbiosis Pune</a>
                        <a href="{{ route('properties.index', ['query' => 'COEP', 'city' => 'Pune']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition">COEP Tech</a>
                        <a href="{{ route('properties.index', ['query' => 'Delhi University', 'city' => 'Delhi']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition">Delhi University</a>
                        <a href="{{ route('properties.index', ['query' => 'IIT Delhi', 'city' => 'Delhi']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition">IIT Delhi</a>
                        <a href="{{ route('properties.index', ['query' => 'Christ', 'city' => 'Bengaluru']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition">Christ Bengaluru</a>
                        <a href="{{ route('properties.index', ['query' => 'Kota', 'city' => 'Kota']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition">Kota Coaching Hub</a>
                        <a href="{{ route('properties.index', ['query' => 'Hazaribagh', 'city' => 'Hazaribagh']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition">VBU Hazaribagh</a>
                    </div>
                </form>
            </div>

            {{-- Real World Trust Strip --}}
            <div class="mt-12 max-w-4xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/50">
                    <div class="text-lg mb-1">📞</div>
                    <div class="text-xs font-bold text-white">Direct Owner Contact</div>
                    <div class="text-[11px] text-slate-400">Call & WhatsApp directly</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/50">
                    <div class="text-lg mb-1">💰</div>
                    <div class="text-xs font-bold text-white">Zero Brokerage</div>
                    <div class="text-[11px] text-slate-400">100% free for all students</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/50">
                    <div class="text-lg mb-1">🔄</div>
                    <div class="text-xs font-bold text-white">360° Virtual Tours</div>
                    <div class="text-[11px] text-slate-400">Inspect room before visit</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/50">
                    <div class="text-lg mb-1">🛡️</div>
                    <div class="text-xs font-bold text-white">Verified Stays</div>
                    <div class="text-[11px] text-slate-400">Owner ID & premise checks</div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
        CATEGORIES / TYPES OF ACCOMMODATION
    ========================================================== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Explore by Accommodation Type</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Filter rooms according to your budget and study routine.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            {{-- PG --}}
            <a href="{{ route('properties.index', ['type' => 'pg']) }}" class="group p-5 rounded-2xl border border-slate-200 bg-white hover:border-slate-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition">
                    🏠
                </div>
                <h3 class="text-sm font-bold text-slate-900">Paying Guest (PG)</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Meals + Cleaning</p>
            </a>

            {{-- Hostel --}}
            <a href="{{ route('properties.index', ['type' => 'hostel']) }}" class="group p-5 rounded-2xl border border-slate-200 bg-white hover:border-slate-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition">
                    🏢
                </div>
                <h3 class="text-sm font-bold text-slate-900">Student Hostels</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Campus Community</p>
            </a>

            {{-- Private Room --}}
            <a href="{{ route('properties.index', ['type' => 'room']) }}" class="group p-5 rounded-2xl border border-slate-200 bg-white hover:border-slate-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition">
                    🛏️
                </div>
                <h3 class="text-sm font-bold text-slate-900">Private Rooms</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Peaceful Study</p>
            </a>

            {{-- Flat --}}
            <a href="{{ route('properties.index', ['type' => 'flat']) }}" class="group p-5 rounded-2xl border border-slate-200 bg-white hover:border-slate-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition">
                    🛋️
                </div>
                <h3 class="text-sm font-bold text-slate-900">Student Flats</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">1BHK & 2BHK</p>
            </a>

            {{-- Roommate Share --}}
            <a href="{{ route('properties.index', ['room_type' => 'double']) }}" class="group p-5 rounded-2xl border border-slate-200 bg-white hover:border-slate-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition">
                    👥
                </div>
                <h3 class="text-sm font-bold text-slate-900">Double Sharing</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Affordable Rent</p>
            </a>

            {{-- Lodge --}}
            <a href="{{ route('properties.index', ['type' => 'lodge']) }}" class="group p-5 rounded-2xl border border-slate-200 bg-white hover:border-slate-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition">
                    🧳
                </div>
                <h3 class="text-sm font-bold text-slate-900">Lodges & Exam Stays</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Short Term Stays</p>
            </a>
        </div>
    </section>

    {{-- =========================================================
        FEATURED VERIFIED STAYS (DYNAMIC DATABASE RECORDS)
    ========================================================== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Verified Student Listings</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Rooms with clear photos, 360° virtual tours, and direct owner phone numbers.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="text-xs font-bold text-slate-900 hover:text-indigo-600 flex items-center gap-1">
                <span>View all listings ({{ $featuredProperties->count() }})</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($featuredProperties as $property)
                <x-property-card :property="$property" />
            @empty
                <div class="p-8 text-center col-span-3 rounded-2xl border border-slate-200 bg-white">
                    <p class="text-sm text-slate-600 font-medium">No properties currently listed in this section.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- =========================================================
        POPULAR STUDENT DESTINATIONS (Real Indian Education Hubs)
    ========================================================== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Popular Student Cities</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Find student rooms near premier colleges, universities, and coaching centers.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $cities = [
                    ['name' => 'Pune', 'hubs' => 'FC Road, Viman Nagar, Kothrud', 'tag' => 'Oxford of the East'],
                    ['name' => 'Delhi', 'hubs' => 'North Campus, South Campus, Laxmi Nagar', 'tag' => 'DU & Central Universities'],
                    ['name' => 'Kota', 'hubs' => 'Landmark City, Vigyan Nagar, Talwandi', 'tag' => 'JEE & NEET Hub'],
                    ['name' => 'Hazaribagh', 'hubs' => 'VBU Campus, Sindur, Kolghati, Matwari', 'tag' => 'Jharkhand University Hub'],
                    ['name' => 'Patna', 'hubs' => 'Boring Road, Kankarbagh, Musallahpur', 'tag' => 'Competitive Coaching Hub'],
                    ['name' => 'Ranchi', 'hubs' => 'Lalpur, Morabadi, BIT Mesra', 'tag' => 'Capital Education Hub'],
                    ['name' => 'Bengaluru', 'hubs' => 'Koramangala, Electronic City, BTM', 'tag' => 'Tech & Management Hub'],
                    ['name' => 'Jaipur', 'hubs' => 'Malviya Nagar, Mansarovar, Gopalpura', 'tag' => 'Rajasthan Universities'],
                ];
            @endphp

            @foreach ($cities as $c)
                <a href="{{ route('properties.index', ['city' => $c['name']]) }}" class="group relative overflow-hidden rounded-2xl p-5 border border-slate-200 bg-white hover:border-slate-400 hover:shadow-md transition-all">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">{{ $c['tag'] }}</div>
                    <h3 class="text-lg font-black text-slate-900 group-hover:text-indigo-600 transition">{{ $c['name'] }}</h3>
                    <p class="text-xs text-slate-500 mt-1 truncate">{{ $c['hubs'] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- =========================================================
        HOW STAYFINDER WORKS (Human, Friendly 2-Column Guide)
    ========================================================== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 sm:p-12 shadow-xs space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100 px-3 py-1 rounded-full">
                    Direct & Transparent Marketplace
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    How StayFinder Works
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    No middlemen. No broker fees. Direct communication between students and accommodation owners.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Column 1: For Students --}}
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 sm:p-8 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg font-bold">
                            🎓
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">For Students & Parents</h3>
                            <span class="text-xs font-bold text-emerald-700">100% Free • No Brokerage Fees</span>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs sm:text-sm text-slate-700">
                        <div class="flex items-start gap-3">
                            <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-800 font-bold flex items-center justify-center shrink-0 text-xs">1</span>
                            <div>
                                <strong class="text-slate-900 block">Search by College or GPS:</strong>
                                Enter your university, coaching centre, or use GPS to locate all registered PGs, hostels, and lodges nearby.
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-800 font-bold flex items-center justify-center shrink-0 text-xs">2</span>
                            <div>
                                <strong class="text-slate-900 block">Inspect with 360° Virtual Tour:</strong>
                                Check the room size, bed condition, study desk, washroom cleanliness, and security before travelling.
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-800 font-bold flex items-center justify-center shrink-0 text-xs">3</span>
                            <div>
                                <strong class="text-slate-900 block">Call or WhatsApp Owner Directly:</strong>
                                Fix your visit date, verify the lodge in person, and move in without paying a single rupee of broker commission.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Column 2: For Providers --}}
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 sm:p-8 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg font-bold">
                            🏨
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">For PG & Lodge Owners</h3>
                            <span class="text-xs font-bold text-indigo-700">Direct Inquiries • Direct Rent</span>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs sm:text-sm text-slate-700">
                        <div class="flex items-start gap-3">
                            <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-800 font-bold flex items-center justify-center shrink-0 text-xs">1</span>
                            <div>
                                <strong class="text-slate-900 block">List in 2 Minutes:</strong>
                                Add your room types (Single, Double, Triple sharing), rates, food menu, and gate closing rules.
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-800 font-bold flex items-center justify-center shrink-0 text-xs">2</span>
                            <div>
                                <strong class="text-slate-900 block">Add Photos & 360° Panorama:</strong>
                                Use your phone camera to capture room photos and 360° panorama to attract 3x more students.
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-800 font-bold flex items-center justify-center shrink-0 text-xs">3</span>
                            <div>
                                <strong class="text-slate-900 block">Receive Direct Phone Calls:</strong>
                                Get genuine student calls and WhatsApp inquiries straight to your phone. Zero commission on student rent.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
        CALL TO ACTION FOR PROVIDERS (Human & Professional)
    ========================================================== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-slate-900 text-white p-8 sm:p-12 lg:p-16 flex flex-col md:flex-row md:items-center md:justify-between gap-8">
            <div class="max-w-xl">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-300 bg-slate-800 px-3 py-1 rounded-full mb-3">
                    🏢 For PG, Hostel & Room Owners
                </span>
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                    Have a PG or Student Accommodation?
                </h2>
                <p class="mt-3 text-sm text-slate-300 leading-relaxed">
                    List your accommodation for free and receive direct phone calls and inquiries from students looking for rooms near your college.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <a href="{{ route('register') }}?role=provider" class="rounded-xl bg-white text-slate-900 px-6 py-3.5 text-sm font-extrabold shadow-md hover:bg-slate-100 transition text-center">
                    List Your Property (Free)
                </a>
                <a href="{{ route('login') }}" class="rounded-xl border border-slate-700 bg-slate-800 px-6 py-3.5 text-sm font-bold text-white hover:bg-slate-700 transition text-center">
                    Owner Sign In
                </a>
            </div>
        </div>
    </section>

    {{-- =========================================================
        SAFETY SECTION (StayFinder Student Safety Shield)
    ========================================================== --}}
    <section id="safety" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-amber-200 bg-amber-50/70 p-8 sm:p-10 text-amber-950">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 rounded-xl bg-amber-200 text-amber-900 flex items-center justify-center font-black">
                    🛡️
                </div>
                <div>
                    <h3 class="text-xl font-bold text-amber-950">StayFinder Student Safety Guidelines</h3>
                    <p class="text-xs text-amber-800">Essential rules to follow before booking any student accommodation.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white p-5 rounded-2xl border border-amber-200/80 shadow-xs">
                    <h4 class="text-sm font-bold text-amber-950 mb-1">1. Never Pay Upfront Online</h4>
                    <p class="text-xs text-amber-900 leading-relaxed">
                        Do not transfer booking advance, token money, or deposit via UPI before visiting the room in person.
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-amber-200/80 shadow-xs">
                    <h4 class="text-sm font-bold text-amber-950 mb-1">2. Verify Food & Electricity</h4>
                    <p class="text-xs text-amber-900 leading-relaxed">
                        Ask specifically if electricity meter is separate or included, check food hygiene, and test WiFi speed.
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-amber-200/80 shadow-xs">
                    <h4 class="text-sm font-bold text-amber-950 mb-1">3. Clarify Notice & Deposit Refund</h4>
                    <p class="text-xs text-amber-900 leading-relaxed">
                        Always verify deposit refund rules, notice period (typically 30 days), and gate closing hours before paying rent.
                    </p>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
    <script>
        function detectHomeLocation(btn) {
            const label = document.getElementById('home-geo-label');
            if (!('geolocation' in navigator)) {
                alert('Geolocation is not supported by your browser.');
                return;
            }
            if (label) label.textContent = '📍 Detecting Area...';
            btn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    document.getElementById('geo-lat').value = pos.coords.latitude;
                    document.getElementById('geo-lng').value = pos.coords.longitude;

                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&zoom=14', { headers: { 'Accept': 'application/json' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            const place = (data.address && (data.address.suburb || data.address.city || data.address.town || data.address.state_district)) || '';
                            const queryInput = document.getElementById('hero-search-query');
                            if (place && queryInput && !queryInput.value) {
                                queryInput.value = place;
                            }
                            btn.closest('form').submit();
                        })
                        .catch(function() {
                            btn.closest('form').submit();
                        });
                },
                function(err) {
                    if (label) label.textContent = '📍 Use Current Location';
                    btn.disabled = false;
                    alert('Location permission was denied or is unavailable. Please enter your college, area or city manually.');
                },
                { timeout: 7000 }
            );
        }
    </script>
@endpush