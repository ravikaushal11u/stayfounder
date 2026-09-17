<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProviderRegisterRequest;
use App\Http\Requests\Auth\StudentRegisterRequest;
use App\Models\ProviderProfile;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show registration form.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getDashboardUrl());
        }

        return view('auth.register');
    }

    /**
     * Register a new student.
     */
    public function registerStudent(StudentRegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::STUDENT,
                'status' => UserStatus::ACTIVE,
                'phone' => $validated['phone'] ?? null,
            ]);

            StudentProfile::create([
                'user_id' => $user->id,
                'gender' => $validated['gender'] ?? null,
                'college_name' => $validated['college_name'] ?? null,
                'course_or_degree' => $validated['course_or_degree'] ?? null,
                'preferred_city' => $validated['preferred_city'] ?? null,
                'budget_min' => $validated['budget_min'] ?? null,
                'budget_max' => $validated['budget_max'] ?? null,
                'preferred_room_type' => $validated['preferred_room_type'] ?? null,
            ]);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('student.dashboard')
            ->with('success', 'Welcome to StayFinder! Your student account has been created.');
    }

    /**
     * Register a new accommodation provider.
     */
    public function registerProvider(ProviderRegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::PROVIDER,
                'status' => UserStatus::ACTIVE,
                'phone' => $validated['phone'],
            ]);

            ProviderProfile::create([
                'user_id' => $user->id,
                'business_name' => $validated['business_name'],
                'owner_name' => $validated['name'],
                'phone' => $validated['phone'],
                'whatsapp_number' => $validated['whatsapp_number'] ?? $validated['phone'],
                'city' => $validated['city'],
                'address' => $validated['address'] ?? null,
                'subscription_tier' => 'free',
                'is_verified' => false,
            ]);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('provider.dashboard')
            ->with('success', 'Welcome to StayFinder! Your provider account has been created. Start listing your properties.');
    }
}
