@extends('layouts.dashboard', ['title' => 'Student Dashboard'])

@section('content')
<div class="space-y-8">
    
    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600 p-6 sm:p-8 text-white shadow-xl shadow-indigo-100">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur-md mb-3 text-indigo-100">
                    <span>🎓 Student Member</span>
                    <span>•</span>
                    <span>{{ $profile?->preferred_city ?? 'City Not Set' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Welcome back, {{ $user->name }}!
                </h1>
                <p class="mt-1 text-sm text-indigo-100 max-w-xl">
                    {{ $profile?->college_name ? 'Enrolled at ' . $profile->college_name : 'Add your college to find verified PGs and hostels within walking distance.' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}#search-section" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-indigo-700 shadow-md hover:bg-indigo-50 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Explore Stays
                </a>
            </div>
        </div>

        {{-- Background decorative shapes --}}
        <div class="absolute -right-10 -bottom-10 h-48 w-48 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
    </div>

    {{-- Profile Completion & Quick Stats Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Profile Completion Card --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Profile Strength</h2>
                <span class="text-xs font-extrabold px-2.5 py-1 rounded-full {{ $profileCompletion > 70 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $profileCompletion }}% Complete
                </span>
            </div>

            <div class="w-full bg-slate-100 rounded-full h-3 mb-4 overflow-hidden">
                <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $profileCompletion }}%"></div>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                A complete student profile helps providers verify your student status and respond to your accommodation inquiries faster.
            </p>

            <div class="text-xs space-y-1.5 text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="{{ $profile?->college_name ? 'text-emerald-500' : 'text-slate-300' }}">✓</span>
                    <span>College: {{ $profile?->college_name ?? 'Not specified' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="{{ $profile?->preferred_city ? 'text-emerald-500' : 'text-slate-300' }}">✓</span>
                    <span>Preferred City: {{ $profile?->preferred_city ?? 'Not specified' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="{{ $user->phone ? 'text-emerald-500' : 'text-slate-300' }}">✓</span>
                    <span>Contact Phone: {{ $user->phone ?? 'Add phone number' }}</span>
                </div>
            </div>
        </div>

        {{-- Quick Stat 1: Saved Stays --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Shortlisted</span>
                    <div class="h-10 w-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900">{{ $savedPropertiesCount }}</div>
                <p class="mt-1 text-xs text-slate-500">Saved student PGs and hostels</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('student.saved-stays') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                    <span>View saved stays ({{ $savedPropertiesCount }})</span>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        {{-- Quick Stat 2: Active Inquiries --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Inquiries</span>
                    <div class="h-10 w-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900">{{ $activeInquiriesCount }}</div>
                <p class="mt-1 text-xs text-slate-500">Direct contacts with accommodation owners</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('student.inquiries') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                    <span>View all inquiries ({{ $activeInquiriesCount }})</span>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Accommodation Preferences Card --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Your Accommodation Preferences</h3>
                <p class="text-xs text-slate-500">We prioritize properties matching these criteria in your search results.</p>
            </div>
            <span class="inline-flex items-center text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                Auto-Applied to Search
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">College Proximity</span>
                <p class="text-sm font-bold text-slate-900 truncate">{{ $profile?->college_name ?? 'Any College' }}</p>
                <span class="text-xs text-indigo-600 font-medium">Within 3 km ideal</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Target City</span>
                <p class="text-sm font-bold text-slate-900">{{ $profile?->preferred_city ?? 'Pan-India' }}</p>
                <span class="text-xs text-slate-500">Primary location</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Budget Range</span>
                <p class="text-sm font-bold text-slate-900">
                    @if ($profile?->budget_max)
                        ₹{{ number_format($profile->budget_min ?? 4000) }} - ₹{{ number_format($profile->budget_max) }}/mo
                    @else
                        ₹5,000 - ₹15,000/mo (Default)
                    @endif
                </p>
                <span class="text-xs text-slate-500">Per student per bed</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Sharing Capacity</span>
                <p class="text-sm font-bold text-slate-900 capitalize">{{ $profile?->preferred_room_type ?? 'Double Sharing' }}</p>
                <span class="text-xs text-slate-500">Preferred occupancy</span>
            </div>
        </div>
    </div>

    {{-- Safety Advisory --}}
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="text-xs sm:text-sm">
                <span class="font-bold text-amber-950">StayFinder Student Protection Rule:</span>
                Never pay an advance, registration fee, or token money without physically visiting the room, checking the washroom and WiFi, and verifying the owner's identity.
            </div>
        </div>
    </div>

</div>
@endsection
