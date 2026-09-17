@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        
        {{-- Header --}}
        <div class="text-center">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-100 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Sign in to StayFinder</h2>
            <p class="mt-2 text-sm text-slate-500">
                New to StayFinder?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition">
                    Create a free account
                </a>
            </p>
        </div>

        @include('components.alert')

        {{-- Login Card --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-100">
            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Email Address</label>
                    <div class="mt-1.5 relative">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email') }}"
                            placeholder="e.g., student@college.edu or provider@pg.com"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden transition @error('email') border-rose-400 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Password</label>
                        <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                    </div>
                    <div class="mt-1.5 relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            placeholder="••••••••"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-hidden transition @error('password') border-rose-400 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="remember" class="ml-2 block text-xs font-medium text-slate-700">
                        Keep me signed in on this device
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                    class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-md shadow-indigo-100 hover:bg-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Sign In
                </button>
            </form>

            {{-- Demo Login Section --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-center text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                    One-Click Quick Demo Sign In
                </p>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('login.demo', 'student') }}"
                        class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 hover:text-indigo-700 transition text-center group">
                        <span class="text-xs font-bold">🎓 Student</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-indigo-500">Aarav (COEP)</span>
                    </a>

                    <a href="{{ route('login.demo', 'provider') }}"
                        class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 hover:text-indigo-700 transition text-center group">
                        <span class="text-xs font-bold">🏨 Provider</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-indigo-500">Sunrise Living</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Safety reminder --}}
        <div class="text-center text-xs text-slate-400">
            Never share your password or OTP. StayFinder will never ask for your confidential information.
        </div>
    </div>
</div>
@endsection
