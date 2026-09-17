@extends('layouts.dashboard', ['title' => 'Student Leads & Inquiries'])

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Student Inquiries & Leads</h1>
            <p class="text-xs sm:text-sm text-slate-500">Contact interested students directly to fill vacant beds.</p>
        </div>
    </div>

    @include('components.alert')

    {{-- Status Filters Tabs --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-bold border-b border-slate-200">
        <a href="{{ route('provider.leads.index') }}"
            class="px-3.5 py-1.5 rounded-xl transition shrink-0 {{ !request('status') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            All Leads ({{ $counts['all'] }})
        </a>
        <a href="{{ route('provider.leads.index', ['status' => 'new']) }}"
            class="px-3.5 py-1.5 rounded-xl transition shrink-0 {{ request('status') === 'new' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            🔥 New Inquiries ({{ $counts['new'] }})
        </a>
        <a href="{{ route('provider.leads.index', ['status' => 'contacted']) }}"
            class="px-3.5 py-1.5 rounded-xl transition shrink-0 {{ request('status') === 'contacted' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Contacted ({{ $counts['contacted'] }})
        </a>
        <a href="{{ route('provider.leads.index', ['status' => 'visit_scheduled']) }}"
            class="px-3.5 py-1.5 rounded-xl transition shrink-0 {{ request('status') === 'visit_scheduled' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Visit Scheduled ({{ $counts['visit_scheduled'] }})
        </a>
        <a href="{{ route('provider.leads.index', ['status' => 'converted']) }}"
            class="px-3.5 py-1.5 rounded-xl transition shrink-0 {{ request('status') === 'converted' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            ✓ Converted / Admitted ({{ $counts['converted'] }})
        </a>
        <a href="{{ route('provider.leads.index', ['status' => 'closed']) }}"
            class="px-3.5 py-1.5 rounded-xl transition shrink-0 {{ request('status') === 'closed' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Closed ({{ $counts['closed'] }})
        </a>
    </div>

    {{-- Filter by Property & Search --}}
    <form method="GET" action="{{ route('provider.leads.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
        @if (request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="sm:col-span-7">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by student name or phone number..."
                class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-xs focus:border-indigo-600 focus:outline-hidden">
        </div>

        <div class="sm:col-span-3">
            <select name="property_id" class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">
                <option value="">All Properties</option>
                @foreach ($myProperties as $p)
                    <option value="{{ $p->id }}" {{ request('property_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <button type="submit" class="w-full rounded-2xl bg-slate-900 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                Filter Leads
            </button>
        </div>
    </form>

    {{-- Leads Cards List --}}
    @if ($leads->count() > 0)
        <div class="space-y-4">
            @foreach ($leads as $lead)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs hover:border-indigo-200 transition space-y-4"
                    x-data="{ editStatus: false, notesOpen: false }">
                    
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 pb-3 border-b border-slate-100">
                        {{-- Left: Student Details --}}
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold border {{ $lead->status_badge_class }}">
                                    {{ $lead->status_label }}
                                </span>
                                <span class="text-[11px] text-slate-400">
                                    Received {{ $lead->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <h3 class="text-base font-extrabold text-slate-900">{{ $lead->student_name }}</h3>
                            
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 mt-1">
                                <span>📱 <strong>{{ $lead->student_phone }}</strong></span>
                                @if ($lead->student_email)
                                    <span>✉️ {{ $lead->student_email }}</span>
                                @endif
                                <span>📅 Move-in: <strong>{{ $lead->target_move_in_date->format('M d, Y') }}</strong></span>
                                @if ($lead->preferred_room_type)
                                    <span>🛏️ <strong>{{ ucfirst($lead->preferred_room_type) }} Sharing</strong></span>
                                @endif
                            </div>

                            <p class="text-xs text-indigo-700 font-semibold mt-1">
                                Inquired for: <a href="{{ route('properties.show', $lead->property->slug) }}" target="_blank" class="underline">{{ $lead->property->title }}</a>
                            </p>
                        </div>

                        {{-- Right: 1-Tap Action Buttons (Call, WhatsApp, Update Status) --}}
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Direct Call --}}
                            <a href="tel:{{ $lead->student_phone }}"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                                <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                Call Student
                            </a>

                            {{-- Direct WhatsApp --}}
                            @php
                                $waPhone = preg_replace('/[^0-9]/', '', $lead->student_phone);
                                $waText = urlencode("Hi {$lead->student_name}, this is {$lead->property->user->name} regarding your inquiry for {$lead->property->title} on StayFinder. When would you like to visit?");
                            @endphp
                            <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50/70 px-3 py-2 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition shadow-2xs">
                                <span>💬 WhatsApp</span>
                            </a>

                            {{-- In-App Chat Shortcut --}}
                            @if ($lead->user_id)
                                @php
                                    $existingConv = \App\Models\Conversation::where('property_id', $lead->property_id)
                                        ->where('student_id', $lead->user_id)
                                        ->where('provider_id', Auth::id())
                                        ->first();
                                @endphp
                                @if ($existingConv)
                                    <a href="{{ route('chat.index', ['conversation_id' => $existingConv->id]) }}"
                                        class="inline-flex items-center gap-1 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition shadow-2xs">
                                        💬 In-App Chat
                                    </a>
                                @endif
                            @endif

                            {{-- Update Status Button --}}
                            <button type="button" @click="editStatus = !editStatus"
                                class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                                Update Status ▾
                            </button>
                        </div>
                    </div>

                    {{-- Student Message --}}
                    @if ($lead->message)
                        <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                            <span class="font-bold text-slate-800 block mb-0.5">Message from student:</span>
                            &ldquo;{{ $lead->message }}&rdquo;
                        </div>
                    @endif

                    {{-- Provider Internal Notes Display --}}
                    @if ($lead->provider_notes)
                        <div class="text-xs text-amber-900 bg-amber-50/70 p-3 rounded-2xl border border-amber-200/60">
                            <span class="font-bold text-amber-950 block mb-0.5">Your Notes:</span>
                            {{ $lead->provider_notes }}
                        </div>
                    @endif

                    {{-- Status Edit Drawer / Form --}}
                    <div x-show="editStatus" x-cloak class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 space-y-3">
                        <form method="POST" action="{{ route('provider.leads.update', $lead->id) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Lead Status</label>
                                    <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800">
                                        <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New Inquiry</option>
                                        <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted (Phone/WhatsApp)</option>
                                        <option value="visit_scheduled" {{ $lead->status === 'visit_scheduled' ? 'selected' : '' }}>Visit Scheduled</option>
                                        <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>Admitted / Converted (Bed Booked)</option>
                                        <option value="closed" {{ $lead->status === 'closed' ? 'selected' : '' }}>Closed / Not Interested</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Private Notes</label>
                                    <input type="text" name="provider_notes" value="{{ $lead->provider_notes }}" placeholder="e.g., Visiting Saturday at 4 PM with father"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs">
                                </div>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="editStatus = false" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 text-xs font-bold text-white shadow-xs hover:bg-indigo-700">
                                    Save Status
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            @endforeach

            <div class="pt-4">
                {{ $leads->links() }}
            </div>
        </div>
    @else
        <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-xs">
            <div class="h-16 w-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl mx-auto mb-4">
                🎯
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">No student inquiries found</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto">
                {{ request('status') ? 'No inquiries matching the selected status.' : 'Inquiries sent by students from your accommodation listings will appear here.' }}
            </p>
        </div>
    @endif

</div>
@endsection
