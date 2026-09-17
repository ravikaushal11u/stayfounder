@php
    $user = Auth::user();
@endphp

<nav class="space-y-6">
    {{-- Role Context Header --}}
    <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-3 text-xs">
        <p class="font-bold text-indigo-900">{{ $user->role->label() }}</p>
        <p class="text-indigo-700/80 text-[11px] truncate">{{ $user->name }}</p>
    </div>

    {{-- Main Nav Items --}}
    <div class="space-y-1">
        @if ($user->isStudent())
            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('student.dashboard') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Overview
            </a>

            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Search Stays
            </a>

            <a href="{{ route('student.saved-stays') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('student.saved-stays') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Saved Stays
                </span>
                <span class="text-xs {{ request()->routeIs('student.saved-stays') ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600' }} px-2 py-0.5 rounded-full">
                    {{ $user->savedProperties()->count() }}
                </span>
            </a>

            <a href="{{ route('student.inquiries') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('student.inquiries') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    My Inquiries
                </span>
                <span class="text-xs {{ request()->routeIs('student.inquiries') ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600' }} px-2 py-0.5 rounded-full">
                    {{ $user->inquiries()->count() }}
                </span>
            </a>

            <a href="{{ route('chat.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('chat.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Direct Messages
                </span>
                @if ($user->unreadMessagesCount() > 0)
                    <span class="text-xs bg-indigo-600 text-white font-bold px-2 py-0.5 rounded-full">
                        {{ $user->unreadMessagesCount() }}
                    </span>
                @endif
            </a>

        @elseif ($user->isProvider())
            <a href="{{ route('provider.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('provider.dashboard') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Overview
            </a>

            <a href="{{ route('provider.properties.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('provider.properties.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    My Properties
                </span>
                <span class="text-xs {{ request()->routeIs('provider.properties.*') ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600' }} px-2 py-0.5 rounded-full">
                    {{ $user->properties()->count() }}
                </span>
            </a>

            <a href="{{ route('provider.leads.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('provider.leads.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Student Leads
                </span>
                <span class="text-xs {{ request()->routeIs('provider.leads.*') ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600' }} px-2 py-0.5 rounded-full">
                    {{ $user->receivedInquiries()->count() }}
                </span>
            </a>

            <a href="{{ route('chat.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('chat.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Direct Messages
                </span>
                @if ($user->unreadMessagesCount() > 0)
                    <span class="text-xs bg-indigo-600 text-white font-bold px-2 py-0.5 rounded-full">
                        {{ $user->unreadMessagesCount() }}
                    </span>
                @endif
            </a>

            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                Analytics
            </a>

            <a href="{{ route('provider.verification.create') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('provider.verification.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Get Verified Badge ✓</span>
            </a>

            <a href="{{ route('provider.subscriptions.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('provider.subscriptions.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <svg class="h-5 w-5 {{ request()->routeIs('provider.subscriptions.*') ? 'text-white' : 'text-amber-500' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Subscription Plans
            </a>

        @elseif ($user->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Admin Overview
            </a>

            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Manage Users
            </a>

            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Verifications
            </a>

            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Reports & Safety
            </a>
        @endif
    </div>

    {{-- Safety Reminder in Sidebar --}}
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
        <p class="font-bold flex items-center gap-1.5 text-amber-800 mb-1">
            <svg class="h-4 w-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Safety First
        </p>
        <p class="text-[11px] text-amber-800 leading-snug">
            Never transfer money or deposit upfront without visiting the property first.
        </p>
    </div>
</nav>
