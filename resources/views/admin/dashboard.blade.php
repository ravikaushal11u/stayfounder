@extends('layouts.dashboard', ['title' => 'Admin Console'])

@section('content')
<div class="space-y-8">
    
    {{-- Admin Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-900 text-white px-3 py-1 text-xs font-bold mb-2">
                🛡️ System Administrator
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">StayFinder Administration Console</h1>
            <p class="text-xs sm:text-sm text-slate-500">Real-time marketplace telemetry, trust & safety moderation, and user management.</p>
        </div>

        <div class="flex items-center gap-3">
            <button class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-xs transition">
                Export Audit Log
            </button>
        </div>
    </div>

    {{-- Platform Metrics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Users --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Users</span>
                <span class="h-8 w-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold">ALL</span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $metrics['total_users'] }}</div>
            <div class="mt-2 text-xs text-slate-500">
                {{ $metrics['total_students'] }} Students &bull; {{ $metrics['total_providers'] }} Providers
            </div>
        </div>

        {{-- Verified Providers --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Verified Providers</span>
                <span class="h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">✓</span>
            </div>
            <div class="text-3xl font-black text-emerald-600">{{ $metrics['verified_providers'] }}</div>
            <div class="mt-2 text-xs text-slate-500">
                {{ $metrics['pending_verifications'] }} verification(s) pending
            </div>
        </div>

        {{-- Active Listings --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Live Stays</span>
                <span class="h-8 w-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">PG</span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $metrics['active_listings'] }}</div>
            <div class="mt-2 text-xs text-slate-500">Across major educational hubs</div>
        </div>

        {{-- Trust & Safety Reports --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Safety Reports</span>
                <span class="h-8 w-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs font-bold">!</span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $metrics['reported_listings'] }}</div>
            <div class="mt-2 text-xs text-emerald-600 font-medium">0 Critical flags pending</div>
        </div>
    </div>

    {{-- Recent Users Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Recent User Registrations</h3>
                <p class="text-xs text-slate-500">Real-time signups across Students and Accommodation Providers.</p>
            </div>
            <span class="text-xs text-indigo-600 font-bold">Showing latest {{ count($recentUsers) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">Role</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Phone</th>
                        <th class="px-6 py-3.5">Registered</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach ($recentUsers as $u)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $u->name }}</p>
                                        <p class="text-slate-400 text-[11px]">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $u->isStudent() ? 'bg-indigo-50 text-indigo-700' : ($u->isProvider() ? 'bg-purple-50 text-purple-700' : 'bg-slate-900 text-white') }}">
                                    {{ $u->role->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    {{ ucfirst($u->status->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $u->phone ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $u->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-indigo-600 hover:text-indigo-900 font-bold">View</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
