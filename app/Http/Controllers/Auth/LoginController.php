<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getDashboardUrl());
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Check if user is active
        if (! $user->isActive()) {
            $status = $user->status instanceof UserStatus ? $user->status->value : (string) $user->status;
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', match ($status) {
                'suspended' => 'Your account has been suspended. Please contact support.',
                'banned' => 'Your account has been permanently banned.',
                default => 'Account is currently inactive.',
            });
        }

        $request->session()->regenerate();

        return redirect()->intended($user->getDashboardUrl())
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Quick demo login for testing student, provider, or admin account.
     */
    public function demoLogin(Request $request, string $role): RedirectResponse
    {
        $email = match ($role) {
            'student' => 'student@stayfinder.com',
            'provider' => 'provider@stayfinder.com',
            'admin' => 'admin@stayfinder.com',
            default => null,
        };

        if (! $email) {
            return redirect()->route('login')->with('error', 'Invalid demo role requested.');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Demo user not seeded yet. Please run seeders.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->getDashboardUrl())->with('success', "Logged in as Demo {$user->role->label()}!");
    }
}
