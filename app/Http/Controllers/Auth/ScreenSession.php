<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ScreenSession extends Controller
{
    public function lock(Request $request)
    {
        // Clear previous lock if somehow partially set
        session()->forget(['screen_locked', 'screen_locked_from']);

        session([
            'screen_locked' => true,
            'screen_locked_from' => url()->previous(),
            'screen_locked_from' => url()->previous() ?? route('welcome'),
        ]);

        return redirect()->route('auth.screen.locked');
    }
    /**
     * Show the screen lock view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */



     public function locked(Request $request)
    {
        if (!session('screen_locked')) {
            return redirect()->route('welcome')->withErrors(['error' => 'Sorry, Your screen is not locked.']);
        }

        return view('auth.lock');
    }

     


    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            session()->forget('screen_locked');
            $redirectTo = session()->pull('screen_locked_from', route('login'));
            return redirect()->to($redirectTo);
        }

        // return back()->with('error', 'Invalid password.');
        return back()->withErrors([
            'password' => 'The provided credentials do not match our records.',
        ])->onlyInput('password');
    }
}

