<footer class="border-t border-slate-200 bg-slate-900 text-slate-400">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        
        {{-- Safety Warning Banner --}}
        <div class="mb-12 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-5 sm:p-6 text-amber-200">
            <div class="flex items-start gap-4">
                <div class="rounded-xl bg-amber-500/20 p-2 text-amber-400 shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-bold text-white mb-1">Student Safety & Scam Prevention First</h4>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        <strong class="text-amber-300">Never send money or booking token amounts</strong> to any provider before physically inspecting the property and meeting the owner or authorized caretaker. StayFinder verifies provider IDs, but you must always exercise personal verification.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-12">
            
            {{-- Col 1: Brand & Mission --}}
            <div class="space-y-4 md:col-span-1">
                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-blue-500 flex items-center justify-center text-white font-bold">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="text-xl font-black text-white">Stay<span class="text-indigo-400">Finder</span></span>
                </div>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                    The open student accommodation marketplace. Find verified PGs, student hostels, flat shares, and rooms directly near colleges with zero student commission.
                </p>
                <div class="text-xs text-slate-500">
                    Empowering students across 50+ educational hubs in India.
                </div>
            </div>

            {{-- Col 2: Student Stays --}}
            <div>
                <h5 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Accommodation Types</h5>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li><a href="{{ route('home') }}?type=pg" class="hover:text-white transition">Boys & Girls PGs</a></li>
                    <li><a href="{{ route('home') }}?type=hostel" class="hover:text-white transition">Student Hostels</a></li>
                    <li><a href="{{ route('home') }}?type=room" class="hover:text-white transition">Single & Shared Rooms</a></li>
                    <li><a href="{{ route('home') }}?type=flat" class="hover:text-white transition">Student Flats & 1BHK/2BHK</a></li>
                    <li><a href="{{ route('home') }}?type=lodge" class="hover:text-white transition">Exam Stays & Lodges</a></li>
                </ul>
            </div>

            {{-- Col 3: Popular Education Hubs --}}
            <div>
                <h5 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Top Student Cities</h5>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li><a href="{{ route('home') }}?city=Pune" class="hover:text-white transition">Pune (FC Road, Viman Nagar, Kothrud)</a></li>
                    <li><a href="{{ route('home') }}?city=Delhi" class="hover:text-white transition">Delhi (North & South Campus, Laxmi Nagar)</a></li>
                    <li><a href="{{ route('home') }}?city=Bengaluru" class="hover:text-white transition">Bengaluru (Koramangala, Electronic City)</a></li>
                    <li><a href="{{ route('home') }}?city=Kota" class="hover:text-white transition">Kota (Indraprastha, Landmark City)</a></li>
                    <li><a href="{{ route('home') }}?city=Jaipur" class="hover:text-white transition">Jaipur & Hyderabad</a></li>
                </ul>
            </div>

            {{-- Col 4: Providers & Safety --}}
            <div>
                <h5 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Providers & Support</h5>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li><a href="{{ route('register') }}?role=provider" class="text-indigo-400 hover:text-indigo-300 font-semibold transition">List Your PG / Hostel</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition">Provider Login</a></li>
                    <li><a href="{{ route('home') }}#safety" class="hover:text-white transition">Trust & Verification Policy</a></li>
                    <li><a href="{{ route('home') }}#faq" class="hover:text-white transition">Frequently Asked Questions</a></li>
                    <li><span class="inline-flex items-center gap-1 text-emerald-400 font-medium">● 100% Free For Students</span></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div>
                &copy; {{ date('Y') }} StayFinder Technologies. Built for students moving to new cities.
            </div>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-slate-400">Terms of Service</a>
                <a href="#" class="hover:text-slate-400">Privacy Policy</a>
                <a href="#" class="hover:text-slate-400">Safety Guidelines</a>
            </div>
        </div>
    </div>
</footer>
