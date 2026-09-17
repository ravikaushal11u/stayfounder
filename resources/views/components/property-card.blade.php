@props(['property'])

<div class="group flex flex-col justify-between rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-lg hover:border-slate-300 transition-all duration-200">
    <div>
        {{-- Image & Badges Container --}}
        <div class="relative h-52 bg-slate-100 overflow-hidden">
            <a href="{{ route('properties.show', $property->slug) }}" class="block w-full h-full">
                <img src="{{ $property->primary_image_url }}" alt="{{ $property->title }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    loading="lazy">
            </a>

            {{-- Badges Overlay --}}
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5 z-10 pointer-events-none">
                @if ($property->is_verified)
                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-700/90 backdrop-blur-xs px-2.5 py-1 text-[11px] font-bold text-white shadow-xs">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        Verified
                    </span>
                @endif

                @if ($property->image_360)
                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-900/85 backdrop-blur-xs px-2.5 py-1 text-[11px] font-bold text-indigo-300 shadow-xs border border-indigo-500/30">
                        <span>🔄</span> 360° Tour
                    </span>
                @endif
            </div>

            {{-- Favorite Heart Button --}}
            <div class="absolute top-3 right-3 z-10">
                <button type="button"
                    onclick="togglePropertyFavorite(this, {{ $property->id }}); event.stopPropagation();"
                    class="h-8 w-8 rounded-full backdrop-blur-xs flex items-center justify-center transition shadow-xs {{ Auth::check() && $property->isSavedBy(Auth::user()) ? 'bg-rose-600 text-white' : 'bg-white/90 text-slate-700 hover:text-rose-600 hover:bg-white' }}"
                    title="Save stay">
                    <svg class="h-4 w-4" fill="{{ Auth::check() && $property->isSavedBy(Auth::user()) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>

            {{-- Bottom Image Tag: Type & Gender --}}
            <div class="absolute bottom-2.5 left-2.5 z-10 flex items-center gap-1.5">
                <span class="rounded-md bg-slate-900/80 backdrop-blur-xs px-2.5 py-0.5 text-[11px] font-semibold text-white">
                    {{ $property->type_label }} &bull; {{ $property->gender_label }}
                </span>
            </div>
        </div>

        {{-- Card Content --}}
        <div class="p-4 sm:p-5">
            {{-- Location & Proximity --}}
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5 gap-2">
                <span class="truncate font-medium flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $property->locality }}, {{ $property->city }}
                </span>

                @if (isset($property->distance_km))
                    <span class="font-bold text-slate-700 shrink-0 bg-slate-100 px-2 py-0.5 rounded text-[11px]">
                        {{ number_format($property->distance_km, 1) }} km away
                    </span>
                @elseif ($property->nearest_college_text)
                    <span class="font-medium text-slate-600 shrink-0 text-[11px] truncate max-w-[140px]" title="{{ $property->nearest_college_text }}">
                        📍 {{ $property->nearest_college_text }}
                    </span>
                @endif
            </div>

            {{-- Property Title --}}
            <h3 class="text-base font-bold text-slate-900 group-hover:text-indigo-600 transition leading-snug">
                <a href="{{ route('properties.show', $property->slug) }}">
                    {{ $property->title }}
                </a>
            </h3>

            {{-- Amenities Snippets --}}
            <div class="mt-2.5 flex flex-wrap gap-1.5 text-[11px] text-slate-600">
                @if ($property->food_included)
                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 font-medium">🍱 Food Included</span>
                @endif
                @foreach ($property->amenities->take(3) as $amenity)
                    <span class="px-2 py-0.5 rounded bg-slate-100 font-medium">{{ $amenity->name }}</span>
                @endforeach
                @if ($property->amenities->count() > 3)
                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-bold">+{{ $property->amenities->count() - 3 }}</span>
                @endif
            </div>

            {{-- Rating & Beds Availability --}}
            <div class="mt-3.5 flex items-center justify-between text-xs text-slate-500 pt-2.5 border-t border-slate-100">
                <div class="flex items-center gap-1">
                    <span class="text-amber-500 font-bold">★</span>
                    <span class="font-bold text-slate-800">{{ number_format($property->rating, 1) }}</span>
                    <span class="text-slate-400">({{ $property->review_count }})</span>
                </div>

                <span class="font-medium {{ $property->available_beds > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                    {{ $property->available_beds > 0 ? $property->available_beds . ' vacant' : 'Full' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Price & CTA Footer --}}
    <div class="px-4 pb-4 sm:px-5 sm:pb-5">
        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <div>
                <div class="text-base font-extrabold text-slate-900">
                    {{ $property->rent_display }}
                </div>
                <div class="text-[11px] text-slate-400">
                    @if ($property->security_deposit > 0)
                        ₹{{ number_format($property->security_deposit) }} Deposit
                    @else
                        Zero Deposit
                    @endif
                </div>
            </div>

            <a href="{{ route('properties.show', $property->slug) }}"
                class="rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs px-4 py-2 transition shadow-xs">
                View Stay &rarr;
            </a>
        </div>
    </div>
</div>
