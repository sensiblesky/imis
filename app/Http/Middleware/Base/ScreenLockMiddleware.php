<?php

namespace App\Http\Middleware\Base;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ScreenLockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && session()->has('screen_locked') && session('screen_locked') === true) {
            return redirect()
                ->route('auth.screen.locked')
                ->with('error', 'Your screen is locked. Please unlock to continue.');
        }

        return $next($request);
    }

}
