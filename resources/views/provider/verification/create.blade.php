@extends('layouts.dashboard', ['title' => 'Get Verified Accommodation Badge'])

@section('content')
<div class="space-y-8 max-w-4xl">
    
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Provider Verification & Trust Badge</h1>
        <p class="text-xs sm:text-sm text-slate-500">Submit your property or business documents to earn the **Verified Student Stay ✓** badge.</p>
    </div>

    @include('components.alert')

    {{-- Trust Badge Benefits Banner --}}
    <div class="rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10 max-w-2xl space-y-3">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 px-3 py-1 text-xs font-bold text-emerald-300">
                <span>✓ Verified Student Stay</span>
            </span>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Why get verified on StayFinder?</h2>
            <p class="text-xs sm:text-sm text-indigo-200 leading-relaxed">
                Students and parents moving from other cities look specifically for Verified listings to avoid advance-deposit scams. Verified listings receive priority ranking on campus proximity searches and up to <strong>3x more student direct inquiries</strong>.
            </p>
        </div>
        <div class="absolute right-4 bottom-4 text-7xl opacity-10 pointer-events-none">
            🛡️
        </div>
    </div>

    {{-- Current Status Card --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl flex items-center justify-center text-2xl {{ $profile?->is_verified ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                {{ $profile?->is_verified ? '✓' : '⏳' }}
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">
                    Account Status: 
                    <span class="{{ $profile?->is_verified ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $profile?->is_verified ? 'Verified ID & Premise ✓' : 'Under Verification / Unverified' }}
                    </span>
                </h4>
                <p class="text-xs text-slate-500">
                    {{ $profile?->verification_notes ?? 'Upload a commercial electricity bill or government ID proof below.' }}
                </p>
            </div>
        </div>

        @if ($profile?->is_verified)
            <span class="inline-block rounded-xl bg-emerald-50 text-emerald-700 font-bold px-3 py-1 text-xs border border-emerald-200">
                Badge Active
            </span>
        @endif
    </div>

    {{-- Upload Verification Request Form --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-extrabold text-slate-900">Upload Verification Document</h3>
            <p class="text-xs text-slate-500">Our safety audit team will inspect your credentials within 24 to 48 hours.</p>
        </div>

        <form action="{{ route('provider.verification.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Document Type --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Document Type *
                    </label>
                    <select name="document_type" required
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-800 focus:border-indigo-600 focus:outline-hidden">
                        <option value="electricity_bill">Commercial Electricity Bill (Recommended)</option>
                        <option value="aadhaar">Government ID / Aadhaar / Voter Card</option>
                        <option value="trade_license">Municipal Trade / Hostel License</option>
                        <option value="property_tax">Property Tax / Rent Deed</option>
                    </select>
                </div>

                {{-- Property (Optional) --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Specific Accommodation (Optional)
                    </label>
                    <select name="property_id"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-800 focus:border-indigo-600 focus:outline-hidden">
                        <option value="">Apply to Entire Provider Account</option>
                        @foreach ($properties as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- File Upload --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Upload Document Copy (PDF, JPG, PNG max 5MB) *
                </label>
                <input type="file" name="document" required accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            {{-- Additional Notes --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Premise Notes / Description
                </label>
                <textarea name="notes" rows="3" placeholder="e.g. Electricity bill is in the name of Rajesh Kumar (owner). Caretaker contact: +91 98765 43210"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs focus:border-indigo-600 focus:outline-hidden"></textarea>
            </div>

            <button type="submit"
                class="rounded-2xl bg-indigo-600 px-6 py-3 text-xs font-bold text-white shadow-md hover:bg-indigo-700 transition">
                Submit for Verification Review
            </button>
        </form>
    </div>

    {{-- Previous Requests History --}}
    @if ($requests->count() > 0)
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Submission History</h3>
            
            <div class="divide-y divide-slate-100 text-xs">
                @foreach ($requests as $req)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <span class="font-bold text-slate-900">{{ $req->document_type_label }}</span>
                            @if ($req->property)
                                <span class="text-slate-500">for {{ $req->property->title }}</span>
                            @endif
                            <p class="text-[11px] text-slate-400 mt-0.5">Submitted {{ $req->created_at->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                {{ $req->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($req->status === 'rejected' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
