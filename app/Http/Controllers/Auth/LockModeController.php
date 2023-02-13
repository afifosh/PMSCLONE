<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LockModeController extends Controller
{
    public function lock()
    {
        if (Auth::guest()) {
            return redirect('/login');
        }

        /**
         * @var \App\Models\User|\App\Models\Admin
         */
        $auth_user = Auth::user();

        $guard = Auth::getDefaultDriver();

        if (!$url = session()->get($auth_user::GET_LOCK_KEY())) {
            $url = url()->previous();
            session()->put($auth_user::GET_LOCK_KEY(), $url);
        }

        return view($guard === 'admin' ? 'admin.auth.lock-mode' : 'auth.lock-mode');
    }

    public function unlock(Request $request)
    {
        if (Auth::guest()) {
            return redirect('/login');
        }

        /**
         * @var \App\Models\User|\App\Models\Admin
         */
        $auth_user = Auth::user();
        $guard = Auth::getDefaultDriver();

        if (! session()->has($auth_user::GET_LOCK_KEY())) {
            return redirect()->route($guard === 'admin' ? 'admin.auth.lock' : 'auth.lock');
        }

        if (Hash::check(trim($request->get('password')), $request->user()->password)) {
            $previous = session()->get($auth_user::GET_LOCK_KEY());

            session()->forget($auth_user::GET_LOCK_KEY());

            return redirect($previous);
        }

        return back()->withInput()->withException(new \Exception('password incorrect'));
    }
}
