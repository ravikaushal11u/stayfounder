@extends('layouts.dashboard', ['title' => 'My Saved Stays'])

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'grid' }">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Saved Accommodations</h1>
            <p class="text-xs sm:text-sm text-slate-500">Shortlisted PGs, hostels, and rooms you want to compare or visit.</p>
        </div>

        @if ($savedProperties->count() > 0)
            {{-- Tab Switcher: Cards vs Side-by-side Comparison --}}
            <div class="flex items-center p-1 bg-slate-200/80 rounded-2xl text-xs font-bold">
                <button type="button" @click="activeTab = 'grid'"
                    :class="activeTab === 'grid' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-xl transition">
                    Cards View ({{ $savedProperties->total() }})
                </button>
                <button type="button" @click="activeTab = 'compare'"
                    :class="activeTab === 'compare' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-xl transition flex items-center gap-1.5">
                    <span>📊 Compare Side-by-Side</span>
                </button>
            </div>
        @endif
    </div>

    @if ($savedProperties->count() > 0)
        
        {{-- TAB 1: CARDS GRID --}}
        <div x-show="activeTab === 'grid'" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($savedProperties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>

            <div class="pt-4">
                {{ $savedProperties->links() }}
            </div>
        </div>

        {{-- TAB 2: SIDE-BY-SIDE COMPARISON TABLE --}}
        <div x-show="activeTab === 'compare'" x-cloak class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs overflow-hidden">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Accommodation Feature Comparison</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="py-3 px-4 text-slate-400 uppercase font-bold text-[11px] bg-slate-50 w-44">Feature</th>
                            @foreach ($savedProperties as $prop)
                                <th class="py-3 px-4 font-bold text-slate-900 min-w-56">
                                    <a href="{{ route('properties.show', $prop->slug) }}" target="_blank" class="hover:text-indigo-600 truncate block">
                                        {{ $prop->title }}
                                    </a>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        {{-- Photo --}}
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-500 bg-slate-50/50">Preview</td>
                            @foreach ($savedProperties as $prop)
                                <td class="py-3 px-4">
                                    <img src="{{ $prop->primary_image_url }}" alt="{{ $prop->title }}" class="h-20 w-32 rounded-xl object-cover">
                                </td>
                            @endforeach
                        </tr>

                        {{-- Type & Gender --}}
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-500 bg-slate-50/50">Type & Gender</td>
                            @foreach ($savedProperties as $prop)
                                <td class="py-3 px-4 font-semibold">
                                    {{ $prop->type_label }} &bull; {{ $prop->gender_label }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Monthly Rent --}}
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-500 bg-slate-50/50">Monthly Rent</td>
                            @foreach ($savedProperties as $prop)
                                <td class="py-3 px-4 text-sm font-black text-indigo-700">
                                    {{ $prop->rent_display }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Security Deposit --}}
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-500 bg-slate-50/50">Security Deposit</td>
                            @foreach ($savedProperties as $prop)
                                <td class="py-3 px-4 font-semibold">
                                    ₹{{ number_format($prop->security_deposit) }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Food / Meals --}}
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-500 bg-slate-50/50">Food Included</td>
                            @foreach ($savedProperties as $prop)
                                <td class="py-3 px-4 font-semibold {{ $prop->food_included ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $prop->food_included ? '✓ Yes (Meals included)' : '✗ Not included' }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Gate Curfew --}}
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-500 bg-slate-50/50">Gate Closing Time</td>
                            @foreach ($savedProperties as $prop)
                                <td class="py-3 px-4 font-semibold">
                                    {{ $prop->gate_closing_time ?? 'No Curfew' }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Vacant Beds --}}
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-500 bg-slate-50/50">Current Vacancy</td>
                            @foreach ($savedProperties as $prop)
                                <td class="py-3 px-4 font-bold {{ $prop->available_beds > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                    {{ $prop->available_beds }} bed(s) vacant
                                </td>
                            @endforeach
                        </tr>

                        {{-- Action CTA --}}
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-500 bg-slate-50/50">Direct Contact</td>
                            @foreach ($savedProperties as $prop)
                                <td class="py-3 px-4">
                                    <a href="{{ route('properties.show', $prop->slug) }}"
                                        class="inline-block rounded-xl bg-indigo-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-indigo-700 transition">
                                        View & Contact
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- Empty State --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-xs">
            <div class="h-16 w-16 rounded-3xl bg-rose-50 text-rose-600 flex items-center justify-center text-3xl mx-auto mb-4">
                ❤️
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">No saved stays yet</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mb-6">
                Click the heart icon on any accommodation card or detail page to shortlist stays and compare them side-by-side.
            </p>
            <a href="{{ route('properties.index') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                Explore Student Stays
            </a>
        </div>
    @endif

</div>
@endsection
