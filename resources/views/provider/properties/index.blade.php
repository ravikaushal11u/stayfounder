@extends('layouts.dashboard', ['title' => 'My Properties'])

@section('content')
<div class="space-y-6">
    
    {{-- Header with Add CTA --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">My Properties & Accommodations</h1>
            <p class="text-xs sm:text-sm text-slate-500">Manage your PGs, hostels, and rooms visible to students.</p>
        </div>

        <a href="{{ route('provider.properties.create') }}"
            class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-xs font-extrabold text-white shadow-md shadow-indigo-100 hover:bg-indigo-700 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            List New Property
        </a>
    </div>

    {{-- Filter Tabs (All / Active / Paused) --}}
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-bold">
        <a href="{{ route('provider.properties.index') }}"
            class="px-3.5 py-1.5 rounded-xl transition {{ !request('status') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            All Listings ({{ $counts['all'] }})
        </a>
        <a href="{{ route('provider.properties.index', ['status' => 'active']) }}"
            class="px-3.5 py-1.5 rounded-xl transition {{ request('status') === 'active' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Active ({{ $counts['active'] }})
        </a>
        <a href="{{ route('provider.properties.index', ['status' => 'paused']) }}"
            class="px-3.5 py-1.5 rounded-xl transition {{ request('status') === 'paused' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Paused ({{ $counts['paused'] }})
        </a>
    </div>

    {{-- Listings Table / Cards --}}
    @if ($properties->count() > 0)
        <div class="rounded-3xl border border-slate-200 bg-white shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Property</th>
                            <th class="px-6 py-3.5">Type & Gender</th>
                            <th class="px-6 py-3.5">Rent / Deposit</th>
                            <th class="px-6 py-3.5">Vacancy</th>
                            <th class="px-6 py-3.5">Views & Leads</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach ($properties as $prop)
                            <tr class="hover:bg-slate-50/70 transition">
                                {{-- Thumbnail & Title --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-14 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                                            <img src="{{ $prop->primary_image_url }}" alt="{{ $prop->title }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 leading-snug">{{ $prop->title }}</p>
                                            <p class="text-slate-400 text-[11px]">📍 {{ $prop->locality }}, {{ $prop->city }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700">
                                        {{ $prop->type_label }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400 mt-0.5">{{ $prop->gender_label }}</span>
                                </td>

                                {{-- Rent --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $prop->rent_display }}</div>
                                    <div class="text-[10px] text-slate-400">Deposit: ₹{{ number_format($prop->security_deposit) }}</div>
                                </td>

                                {{-- Vacancy --}}
                                <td class="px-6 py-4">
                                    <span class="font-bold {{ $prop->available_beds > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                        {{ $prop->available_beds }} / {{ $prop->total_beds }} beds
                                    </span>
                                    <span class="block text-[10px] text-slate-400">{{ $prop->rooms->count() }} room types</span>
                                </td>

                                {{-- Views & Inquiries --}}
                                <td class="px-6 py-4">
                                    <div class="text-slate-900 font-bold">{{ $prop->views_count }} views</div>
                                    <div class="text-[10px] text-indigo-600 font-semibold">{{ $prop->inquiries_count }} student inquiries</div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $prop->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $prop->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ ucfirst($prop->status) }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('properties.show', $prop->slug) }}" target="_blank"
                                        class="text-indigo-600 hover:text-indigo-900 font-bold" title="View Public Page">
                                        View
                                    </a>

                                    <a href="{{ route('provider.properties.edit', $prop->id) }}"
                                        class="text-slate-700 hover:text-slate-900 font-bold" title="Edit Listing">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('provider.properties.toggle-status', $prop->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-amber-600 hover:text-amber-800 font-bold text-xs" title="Toggle status">
                                            {{ $prop->status === 'active' ? 'Pause' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('provider.properties.destroy', $prop->id) }}" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this property listing?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100">
                {{ $properties->links() }}
            </div>
        </div>
    @else
        <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-xs">
            <div class="h-14 w-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-3">
                🏠
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">No property listings found</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mb-6">
                Start listing your PGs, student rooms, or hostels to reach students moving to colleges in your area.
            </p>
            <a href="{{ route('provider.properties.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                Create First Listing
            </a>
        </div>
    @endif

</div>
@endsection
