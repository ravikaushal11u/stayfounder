<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isActive()) {
            $status = $user->status instanceof UserStatus ? $user->status->value : (string) $user->status;
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', match ($status) {
                'suspended' => 'Your account is suspended. Please contact StayFinder support.',
                'banned' => 'Your account has been banned due to policy violations.',
                default => 'Your account is inactive.',
            });
        }

        return $next($request);
    }
}
