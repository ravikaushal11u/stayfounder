@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-8rem)] py-12 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto" x-data="{ selectedRole: '{{ request('role') === 'provider' ? 'provider' : 'student' }}' }">
    
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-100 mb-4">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create your StayFinder Account</h2>
        <p class="mt-2 text-sm text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition">
                Sign in here
            </a>
        </p>
    </div>

    @include('components.alert')

    {{-- Role Selection Pill Tabs --}}
    <div class="mb-8 grid grid-cols-2 gap-3 p-1.5 bg-slate-200/70 rounded-2xl">
        <button type="button" @click="selectedRole = 'student'"
            :class="selectedRole === 'student' ? 'bg-white text-indigo-700 shadow-md font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
            class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
            </svg>
            <span>I'm a Student (100% Free)</span>
        </button>

        <button type="button" @click="selectedRole = 'provider'"
            :class="selectedRole === 'provider' ? 'bg-white text-indigo-700 shadow-md font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
            class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>I'm a Provider (PG/Hostel Owner)</span>
        </button>
    </div>

    {{-- Form Container --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xl shadow-slate-100">
        
        {{-- STUDENT REGISTRATION FORM --}}
        <div x-show="selectedRole === 'student'" x-transition>
            <div class="mb-6 pb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Student Account Details</h3>
                <p class="text-xs text-slate-500">Free forever. Connect directly with accommodation providers near your college.</p>
            </div>

            <form method="POST" action="{{ route('register.student') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Full Name --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Aarav Sharma"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="student@example.com"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Phone Number --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Gender Preference</label>
                        <select name="gender" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- College / University --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">College / Coaching Institute</label>
                        <input type="text" name="college_name" value="{{ old('college_name') }}" placeholder="e.g., COEP Pune, DU, IIT Delhi"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>

                    {{-- Preferred City --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Target City</label>
                        <input type="text" name="preferred_city" value="{{ old('preferred_city') }}" placeholder="e.g., Pune, Delhi, Kota"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password *</label>
                        <input type="password" name="password" required placeholder="Minimum 8 characters"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required placeholder="Re-enter password"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 py-3.5 text-sm font-bold text-white shadow-md shadow-indigo-100 hover:bg-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        Create Free Student Account
                    </button>
                </div>
            </form>
        </div>

        {{-- PROVIDER REGISTRATION FORM --}}
        <div x-show="selectedRole === 'provider'" x-transition>
            <div class="mb-6 pb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Provider Business Account</h3>
                <p class="text-xs text-slate-500">Reach verified students searching for PGs, hostels, flats, and rooms.</p>
            </div>

            <form method="POST" action="{{ route('register.provider') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Contact / Owner Name --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Owner / Manager Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Rajesh Kumar"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>

                    {{-- Business / PG Name --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">PG / Hostel / Property Name *</label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}" required placeholder="e.g., Sunrise Student PG"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="provider@stayfinder.com"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>

                    {{-- Phone Number --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Phone (For Student Calls) *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+91 98765 43210"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- WhatsApp Number --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">WhatsApp Number (Optional)</label>
                        <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="+91 98765 43210"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">City *</label>
                        <input type="text" name="city" value="{{ old('city') }}" required placeholder="e.g., Pune, Delhi, Kota"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>
                </div>

                {{-- Property Address --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Locality / Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="e.g., Plot 14, Near Symbiosis College, Viman Nagar"
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password *</label>
                        <input type="password" name="password" required placeholder="Minimum 8 characters"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required placeholder="Re-enter password"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 py-3.5 text-sm font-bold text-white shadow-md shadow-indigo-100 hover:bg-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        Register as Accommodation Provider
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- Trust footnote --}}
    <div class="mt-6 text-center text-xs text-slate-500">
        By registering, you agree to StayFinder's Student Safety Guidelines and Terms of Service.
    </div>
</div>
@endsection
