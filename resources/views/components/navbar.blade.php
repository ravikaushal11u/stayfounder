<nav x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur-md">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            
            {{-- Logo --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="h-9 w-9 rounded-xl bg-slate-900 flex items-center justify-center text-white shadow-xs group-hover:bg-indigo-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xl font-black tracking-tight text-slate-900">Stay<span class="text-indigo-600">Finder</span></span>
                        <span class="hidden sm:inline-block text-[10px] font-bold uppercase tracking-wider text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">Student Stays</span>
                    </div>
                </a>

                {{-- Desktop Links --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                        Explore
                    </a>
                    <a href="{{ route('home') }}#search-section" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                        Find a Stay
                    </a>
                    <a href="{{ route('register') }}?role=provider" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                        For PG Owners
                    </a>
                    <a href="{{ route('home') }}#safety" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                        Safety & Trust
                    </a>
                </div>
            </div>

            {{-- Right Section / Auth Actions --}}
            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900 px-3 py-2 rounded-lg transition">
                        Sign in
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-slate-800 transition">
                        Student Sign up
                    </a>
                    <a href="{{ route('register') }}?role=provider" class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-100 hover:border-slate-400 transition">
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        List PG / Room
                    </a>
                @else
                    {{-- Messages Icon Button --}}
                    @php
                        $unreadMsgCount = Auth::user()->unreadMessagesCount();
                    @endphp
                    <a href="{{ route('chat.index') }}" class="relative rounded-full p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition" title="Direct Messages">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        @if ($unreadMsgCount > 0)
                            <span class="absolute top-1 right-1 h-4 w-4 bg-indigo-600 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center ring-2 ring-white">
                                {{ $unreadMsgCount > 9 ? '9+' : $unreadMsgCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Role Badge & Dashboard Shortcut --}}
                    <div class="relative" @click.outside="userDropdownOpen = false">
                        <button @click="userDropdownOpen = !userDropdownOpen" type="button" class="flex items-center gap-2.5 rounded-full p-1 text-left focus:outline-hidden hover:bg-slate-50 transition pr-2">
                            <div class="h-9 w-9 rounded-full bg-slate-900 text-white font-bold text-sm flex items-center justify-center shadow-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden lg:block text-left">
                                <div class="text-xs font-bold text-slate-900 leading-tight truncate max-w-[130px]">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">{{ Auth::user()->role->label() }}</div>
                            </div>
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="userDropdownOpen" x-transition.origin.top.right class="absolute right-0 mt-2 w-56 rounded-2xl bg-white p-2 shadow-xl ring-1 ring-slate-900/5 z-50 divide-y divide-slate-100">
                            <div class="px-3 py-2">
                                <p class="text-xs text-slate-500">Signed in as</p>
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                                <span class="mt-1 inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                    {{ Auth::user()->role->label() }}
                                </span>
                            </div>

                            <div class="py-1">
                                <a href="{{ Auth::user()->getDashboardUrl() }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    Dashboard
                                </a>

                                <a href="{{ route('chat.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        Messages
                                    </div>
                                    @if ($unreadMsgCount > 0)
                                        <span class="rounded-full bg-indigo-600 text-white text-[10px] font-bold px-1.5 py-0.5">{{ $unreadMsgCount }}</span>
                                    @endif
                                </a>

                                @if (Auth::user()->isStudent())
                                    <a href="{{ route('student.saved-stays') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                        <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        Saved Stays
                                    </a>
                                @endif

                                @if (Auth::user()->isProvider())
                                    <a href="{{ route('provider.properties.create') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        + List New Stay
                                    </a>
                                @endif
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 transition">
                                        <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest

                {{-- Mobile Menu Trigger --}}
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden rounded-xl p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Nav Flyout --}}
    <div x-show="mobileMenuOpen" x-transition class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-4 space-y-3">
        <a href="{{ route('home') }}" class="block text-sm font-semibold text-slate-700 py-1.5">
            Explore Stays
        </a>
        <a href="{{ route('home') }}#search-section" class="block text-sm font-semibold text-slate-700 py-1.5">
            Search by City / College
        </a>
        <a href="{{ route('register') }}?role=provider" class="block text-sm font-semibold text-slate-700 py-1.5">
            List PG / Hostel (Owners)
        </a>
        <a href="{{ route('home') }}#safety" class="block text-sm font-semibold text-slate-700 py-1.5">
            Safety & Scam Warnings
        </a>

        @guest
            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                <a href="{{ route('login') }}" class="flex-1 text-center rounded-xl border border-slate-300 py-2 text-xs font-bold text-slate-700">
                    Sign in
                </a>
                <a href="{{ route('register') }}" class="flex-1 text-center rounded-xl bg-slate-900 py-2 text-xs font-bold text-white">
                    Sign up
                </a>
            </div>
        @endguest
    </div>
</nav>
