@extends('layouts.dashboard', ['title' => 'My Inquiries'])

@section('content')
<div class="space-y-8">
    
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">My Inquiries & Contact Requests</h1>
        <p class="text-xs sm:text-sm text-slate-500">Track all accommodations you have inquired about and view provider replies.</p>
    </div>

    @include('components.alert')

    @if ($inquiries->count() > 0)
        <div class="space-y-4">
            @foreach ($inquiries as $inquiry)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-6 hover:border-indigo-200 transition">
                    
                    {{-- Left: Property Thumbnail & Details --}}
                    <div class="flex items-start gap-4">
                        <div class="h-16 w-20 rounded-2xl bg-slate-100 overflow-hidden shrink-0">
                            <img src="{{ $inquiry->property->primary_image_url }}" alt="{{ $inquiry->property->title }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold border {{ $inquiry->status_badge_class }}">
                                    {{ $inquiry->status_label }}
                                </span>
                                <span class="text-[11px] text-slate-400">
                                    Inquired on {{ $inquiry->created_at->format('M d, Y') }}
                                </span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900">
                                <a href="{{ route('properties.show', $inquiry->property->slug) }}" class="hover:text-indigo-600 transition">
                                    {{ $inquiry->property->title }}
                                </a>
                            </h3>

                            <p class="text-xs text-slate-500 mt-0.5">
                                📍 {{ $inquiry->property->locality }}, {{ $inquiry->property->city }} &bull;
                                Target Move-in: <strong class="text-slate-800">{{ $inquiry->target_move_in_date->format('M d, Y') }}</strong>
                                @if ($inquiry->preferred_room_type)
                                    &bull; Preferred: <strong class="text-slate-800">{{ ucfirst($inquiry->preferred_room_type) }} Sharing</strong>
                                @endif
                            </p>

                            @if ($inquiry->message)
                                <p class="text-xs text-slate-600 mt-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 max-w-xl">
                                    &ldquo;{{ $inquiry->message }}&rdquo;
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Right: Provider Details & Contact Buttons --}}
                    <div class="flex flex-col sm:flex-row md:flex-col items-stretch sm:items-center md:items-end gap-2 shrink-0 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100">
                        @php
                            $providerPhone = $inquiry->provider?->providerProfile?->phone ?? $inquiry->provider?->phone;
                            $whatsappNumber = $inquiry->provider?->providerProfile?->whatsapp_number ?? $providerPhone;
                        @endphp

                        <div class="text-xs text-slate-500 mb-1 text-right hidden md:block">
                            <span>Provider: <strong class="text-slate-900">{{ $inquiry->provider?->name }}</strong></span>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            @if ($providerPhone)
                                <a href="tel:{{ $providerPhone }}"
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                                    <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    Call
                                </a>
                            @endif

                            @if ($whatsappNumber)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}?text={{ urlencode('Hi! I submitted an inquiry for ' . $inquiry->property->title . ' on StayFinder.') }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50/70 px-3.5 py-2 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition shadow-2xs">
                                    <span>💬 WhatsApp</span>
                                </a>
                            @endif

                            <a href="{{ route('properties.show', $inquiry->property->slug) }}"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                                View Stay
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach

            <div class="pt-4">
                {{ $inquiries->links() }}
            </div>
        </div>
    @else
        <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-xs">
            <div class="h-16 w-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl mx-auto mb-4">
                📬
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">No active inquiries</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mb-6">
                When you find an accommodation you like, click "Chat with Provider" or send an inquiry to contact the owner directly.
            </p>
            <a href="{{ route('properties.index') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                Browse Stays Near Your College
            </a>
        </div>
    @endif

</div>
@endsection
