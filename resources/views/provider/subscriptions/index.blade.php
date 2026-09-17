@extends('layouts.dashboard', ['title' => 'Provider Subscription Plans - StayFinder'])

@section('content')
<div class="space-y-8 max-w-6xl" x-data="{
    checkoutModalOpen: false,
    selectedPlan: null,
    orderId: '',
    paymentLoading: false,
    paymentSuccess: false,
    errorMessage: '',

    openCheckout(plan) {
        this.selectedPlan = plan;
        this.errorMessage = '';
        this.paymentLoading = true;
        this.checkoutModalOpen = true;

        fetch('{{ url('/provider/subscriptions/checkout') }}/' + plan.id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            this.paymentLoading = false;
            if (data.is_free) {
                window.location.reload();
                return;
            }
            if (data.success) {
                this.orderId = data.order_id;
            } else {
                this.errorMessage = data.message || 'Error creating order.';
            }
        })
        .catch(err => {
            this.paymentLoading = false;
            this.errorMessage = 'Network error connecting to payment gateway.';
        });
    },

    completeMockPayment() {
        this.paymentLoading = true;
        fetch('{{ route('provider.subscriptions.verify') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: this.orderId,
                payment_id: 'pay_mock_' + Math.random().toString(36).substring(2, 10),
                signature: 'mock_sig_' + this.orderId,
                plan_id: this.selectedPlan.id
            })
        })
        .then(res => res.json())
        .then(data => {
            this.paymentLoading = false;
            if (data.success) {
                this.paymentSuccess = true;
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                this.errorMessage = data.message || 'Verification failed.';
            }
        })
        .catch(err => {
            this.paymentLoading = false;
            this.errorMessage = 'Error verifying payment.';
        });
    }
}">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Provider Subscription & Visibility Plans</h1>
            <p class="text-xs sm:text-sm text-slate-500">Upgrade your listings to verified status, reach 3x more students, and capture priority leads.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="rounded-xl bg-emerald-50 text-emerald-700 px-3 py-1.5 text-xs font-bold border border-emerald-200">
                ● 100% Free For Students
            </span>
        </div>
    </div>

    @include('components.alert')

    {{-- Zero Commission Philosophy Banner --}}
    <div class="rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10 max-w-2xl space-y-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/30 border border-indigo-400/40 px-3 py-1 text-xs font-bold text-indigo-300">
                <span>🛡️ Zero Brokerage Guarantee</span>
            </span>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">You Keep 100% of Every Student's Rent</h2>
            <p class="text-xs sm:text-sm text-indigo-200 leading-relaxed">
                StayFinder never takes a percentage cut from student deposits or monthly rent. You only pay a flat, predictable listing subscription to get priority visibility on nearby college campuses.
            </p>
        </div>
        <div class="absolute right-4 bottom-2 text-8xl opacity-10 pointer-events-none">
            💎
        </div>
    </div>

    {{-- Current Active Plan Card --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                ⭐
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Current Plan</span>
                <h3 class="text-lg font-black text-slate-900">
                    {{ $activePlan?->name ?? 'Free Starter' }}
                    @if ($currentSubscription && $currentSubscription->isActive())
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 ml-2">
                            Active
                        </span>
                    @endif
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if ($currentSubscription && $currentSubscription->ends_at)
                        Renews on {{ $currentSubscription->ends_at->format('M d, Y') }} ({{ $currentSubscription->daysRemaining() }} days left)
                    @else
                        Active indefinitely with standard search priority.
                    @endif
                </p>
            </div>
        </div>

        <div class="text-left sm:text-right">
            <span class="text-xs text-slate-500 block">Accommodations Limit</span>
            <span class="text-sm font-extrabold text-slate-900">
                {{ $user->properties()->count() }} / {{ $activePlan?->listing_limit ?? 1 }} Listed
            </span>
        </div>
    </div>

    {{-- 3-Tier Subscription Plans Grid --}}
    <div>
        <div class="text-center max-w-xl mx-auto mb-8">
            <h2 class="text-xl font-black text-slate-900">Choose Your Campus Visibility Tier</h2>
            <p class="text-xs text-slate-500 mt-1">Upgrade or change your plan at any time. Secure payment via Razorpay (UPI, Cards, Netbanking).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
            @foreach ($plans as $plan)
                @php
                    $isCurrent = ($activePlan && $activePlan->id === $plan->id);
                @endphp
                <div class="relative rounded-3xl border {{ $plan->is_featured ? 'border-indigo-600 ring-2 ring-indigo-600 shadow-xl bg-indigo-50/20' : 'border-slate-200 bg-white shadow-xs' }} p-6 sm:p-8 flex flex-col justify-between transition hover:-translate-y-1">
                    
                    @if ($plan->is_featured)
                        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-wider py-1 px-4 rounded-full shadow-md">
                            Most Popular for PG Owners
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">{{ $plan->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $plan->price <= 0 ? 'Ideal for independent single-room landlords.' : ($plan->slug === 'pro' ? 'Best for single PGs and student hostels.' : 'For multi-branch hostels and residency chains.') }}
                            </p>
                        </div>

                        <div class="py-2 border-y border-slate-100">
                            <span class="text-3xl font-black text-slate-900">{{ $plan->price <= 0 ? '₹0' : '₹' . number_format($plan->price) }}</span>
                            <span class="text-xs text-slate-500 font-bold">/ month</span>
                        </div>

                        {{-- Features List --}}
                        <ul class="space-y-2.5 text-xs text-slate-700">
                            @foreach ((array) $plan->features as $feature)
                                <li class="flex items-start gap-2.5">
                                    <svg class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-100">
                        @if ($isCurrent)
                            <button type="button" disabled
                                class="w-full rounded-2xl bg-slate-100 py-3 text-xs font-bold text-slate-400 cursor-not-allowed">
                                Current Active Plan
                            </button>
                        @else
                            <button type="button" @click="openCheckout({{ json_encode($plan) }})"
                                class="w-full rounded-2xl {{ $plan->is_featured ? 'bg-indigo-600 text-white shadow-md hover:bg-indigo-700' : 'bg-slate-900 text-white hover:bg-slate-800' }} py-3 text-xs font-bold transition">
                                {{ $plan->price <= 0 ? 'Downgrade to Free' : 'Upgrade to ' . $plan->name }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Billing & Transaction History --}}
    @if ($transactions->count() > 0)
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Recent Payment Transactions</h3>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach ($transactions as $txn)
                    <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="font-bold text-slate-900">Order: {{ $txn->order_id }}</span>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $txn->created_at->format('M d, Y • h:i A') }} • Payment ID: {{ $txn->payment_id ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="text-sm font-black text-slate-900">₹{{ number_format($txn->amount, 2) }}</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                {{ $txn->status === 'captured' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($txn->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Razorpay Simulation Checkout Modal --}}
    <div x-show="checkoutModalOpen" class="relative z-50" role="dialog" aria-modal="true" x-cloak>
        <div x-show="checkoutModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20 flex items-center justify-center">
            <div x-show="checkoutModalOpen" @click.outside="if (!paymentLoading) checkoutModalOpen = false"
                class="w-full max-w-md rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="h-8 w-8 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">₹</span>
                        <h3 class="text-base font-extrabold text-slate-900">Razorpay Secure Checkout</h3>
                    </div>
                    <button @click="checkoutModalOpen = false" :disabled="paymentLoading" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                {{-- Loading State --}}
                <div x-show="paymentLoading" class="py-8 text-center space-y-3">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mx-auto"></div>
                    <p class="text-xs font-bold text-slate-700">Connecting to Razorpay Gateway...</p>
                    <p class="text-[11px] text-slate-400">Please wait while we prepare your secure transaction.</p>
                </div>

                {{-- Success State --}}
                <div x-show="paymentSuccess" class="py-8 text-center space-y-3">
                    <div class="h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-xl font-bold">✓</div>
                    <h4 class="font-bold text-slate-900">Payment Authorized & Captured!</h4>
                    <p class="text-xs text-slate-500">Your subscription tier has been upgraded. Reloading dashboard...</p>
                </div>

                {{-- Payment Options Form --}}
                <div x-show="!paymentLoading && !paymentSuccess" class="space-y-4 mt-4">
                    <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-indigo-950 block" x-text="selectedPlan?.name"></span>
                            <span class="text-[11px] text-indigo-700">30-Day Accommodation Access</span>
                        </div>
                        <span class="text-lg font-black text-indigo-900" x-text="'₹' + selectedPlan?.price"></span>
                    </div>

                    <div x-show="errorMessage" class="p-3 rounded-xl bg-rose-50 text-rose-700 text-xs font-medium" x-text="errorMessage"></div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Select Payment Method</label>
                        <div class="space-y-2 text-xs">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-indigo-600 bg-indigo-50/30 cursor-pointer">
                                <input type="radio" name="paymethod" checked class="text-indigo-600">
                                <span class="font-bold text-slate-900">Instant UPI (GPay, PhonePe, Paytm)</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                                <input type="radio" name="paymethod" class="text-indigo-600">
                                <span class="font-bold text-slate-700">Credit / Debit Card (Visa, RuPay, Master)</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                                <input type="radio" name="paymethod" class="text-indigo-600">
                                <span class="font-bold text-slate-700">Net Banking (HDFC, ICICI, SBI)</span>
                            </label>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-100 text-[11px] text-slate-500">
                        🔒 Razorpay Sandbox Test Mode enabled. Clicking Pay will simulate payment authorization and generate a valid HMAC signature.
                    </div>

                    <button type="button" @click="completeMockPayment()"
                        class="w-full rounded-2xl bg-indigo-600 py-3 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-md flex items-center justify-center gap-2">
                        <span>Pay ₹<span x-text="selectedPlan?.price"></span> with Razorpay</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
