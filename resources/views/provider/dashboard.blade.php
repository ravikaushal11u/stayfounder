@extends('layouts.dashboard', ['title' => 'Provider Dashboard'])

@section('content')
<div class="space-y-8">
    
    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold backdrop-blur-md text-slate-200">
                        🏢 Provider Portal
                    </span>
                    @if ($profile?->is_verified)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 px-3 py-1 text-xs font-bold text-emerald-300">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Verified Provider
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/20 border border-amber-400/30 px-3 py-1 text-xs font-bold text-amber-300">
                            Verification Pending
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    {{ $profile?->business_name ?? $user->name }}
                </h1>
                <p class="mt-1 text-sm text-slate-300">
                    📍 {{ $profile?->city ?? 'Location not specified' }} &bull; Response rate: <strong class="text-white">{{ $profile?->response_rate ?? 100 }}%</strong>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('provider.properties.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    List New Property
                </a>
            </div>
        </div>
    </div>

    {{-- Subscription & Boost Banner --}}
    <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-blue-50 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shrink-0 shadow-md shadow-indigo-100">
                ⭐
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Plan Tier:</span>
                    <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full {{ $profile?->isPremium() ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ strtoupper($profile?->subscription_tier ?? 'FREE') }}
                    </span>
                </div>
                <p class="text-sm font-bold text-slate-900 mt-0.5">
                    {{ $profile?->isPremium() ? 'Premium Visibility Active: Priority Search Placement' : 'Upgrade to Premium for 5x more student inquiries and Verified Badge' }}
                </p>
            </div>
        </div>
        <a href="{{ route('provider.properties.create') }}" class="shrink-0 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition shadow-xs text-center">
            List New Stay
        </a>
    </div>

    {{-- Metrics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Listings --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Properties</span>
                <div class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-900">{{ $stats['total_listings'] }}</div>
            <div class="mt-2 text-xs text-slate-500">
                <span class="text-emerald-600 font-bold">{{ $stats['active_listings'] }} Active</span> &bull;
                <span>{{ $stats['total_listings'] - $stats['active_listings'] }} Paused</span>
            </div>
        </div>

        {{-- Total Views --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Student Views</span>
                <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_views']) }}</div>
            <div class="mt-2 text-xs text-emerald-600 font-medium">Search impressions</div>
        </div>

        {{-- Direct Inquiries --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Inquiries & Chats</span>
                <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-900">{{ $stats['total_inquiries'] }}</div>
            <div class="mt-2 text-xs text-slate-500">From verified students</div>
        </div>

        {{-- Call & WhatsApp Leads --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Direct Calls</span>
                <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-900">{{ $stats['total_calls'] }}</div>
            <div class="mt-2 text-xs text-emerald-600 font-medium">Direct phone/WhatsApp clicks</div>
        </div>
    </div>

    {{-- Recent Properties List --}}
    @if ($recentProperties->count() > 0)
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900">Recent Accommodations</h3>
                <a href="{{ route('provider.properties.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    View All ({{ $stats['total_listings'] }}) →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($recentProperties as $prop)
                    <div class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-slate-50/50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold {{ $prop->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ ucfirst($prop->status) }}
                                </span>
                                <span class="text-xs font-bold text-slate-900">{{ $prop->rent_display }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 truncate">{{ $prop->title }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">📍 {{ $prop->locality }}, {{ $prop->city }}</p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-500">{{ $prop->views_count }} views</span>
                            <div class="space-x-2">
                                <a href="{{ route('provider.properties.edit', $prop->id) }}" class="font-bold text-slate-700 hover:text-indigo-600">Edit</a>
                                <a href="{{ route('properties.show', $prop->slug) }}" target="_blank" class="font-bold text-indigo-600">Preview ↗</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Getting Started Walkthrough Card --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Get Started: List Your Accommodation in 3 Steps</h3>
        <p class="text-xs text-slate-500 mb-6">Attract students moving to colleges in your area with a complete listing.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-2xl border border-slate-100 p-5 bg-slate-50/70">
                <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs mb-3">
                    1
                </div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Add Property Details</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Set property type (PG, Hostel, Flat, Room), nearby colleges, and GPS map pin.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 p-5 bg-slate-50/70">
                <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs mb-3">
                    2
                </div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Upload Real Photos</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Clear photos of the bedrooms, bathrooms, study area, and dining space boost inquiries by 300%.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 p-5 bg-slate-50/70">
                <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs mb-3">
                    3
                </div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Get Verified Badge</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Submit ID proof to get a green Verified Badge and build immediate trust with parents & students.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
