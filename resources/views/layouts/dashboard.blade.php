<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - StayFinder</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: false }" class="h-full antialiased text-slate-800 bg-slate-50">

    {{-- Top Navbar --}}
    <header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-slate-200 bg-white px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:px-8">
        <button @click="sidebarOpen = true" type="button" class="-m-2.5 p-2.5 text-slate-700 lg:hidden">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white shadow-xs">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">Stay<span class="text-indigo-600">Finder</span></span>
            </a>
            <span class="hidden sm:inline-block text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                {{ Auth::user()->role->label() }} Portal
            </span>
        </div>

        <div class="flex flex-1 justify-end items-center gap-x-4">
            <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-indigo-600 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Website
            </a>

            <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

            {{-- User Pill --}}
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold text-sm flex items-center justify-center shadow-xs">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="hidden md:block text-left text-xs">
                    <p class="font-bold text-slate-900">{{ Auth::user()->name }}</p>
                    <p class="text-slate-500">{{ Auth::user()->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" title="Sign out" class="rounded-lg p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex min-h-[calc(100vh-4rem)]">
        
        {{-- Desktop Sidebar Navigation --}}
        <aside class="hidden lg:flex lg:w-64 lg:flex-col border-r border-slate-200 bg-white p-6 shrink-0">
            @include('layouts.partials.dashboard-nav')
        </aside>

        {{-- Mobile Sidebar Drawer --}}
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" @click.outside="sidebarOpen = false" class="relative mr-16 flex w-full max-w-xs flex-1 bg-white p-6">
                    <div class="w-full">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                            <span class="text-base font-bold text-slate-900">Menu</span>
                            <button @click="sidebarOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        @include('layouts.partials.dashboard-nav')
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Dashboard Body --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
            <div class="mx-auto max-w-7xl">
                @include('components.alert')
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>
