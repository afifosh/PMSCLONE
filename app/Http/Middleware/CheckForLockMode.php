<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckForLockMode
{
    protected $except = [
        '/login',
        '/logout',
        '/auth/lock',
        '/auth/unlock',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * @var \App\Models\User|\App\Models\Admin
         */
        $auth_user = Auth::user();

        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }

        if ($request->session()->has($auth_user::GET_LOCK_KEY())) {
            return redirect()->route(
                Auth::getDefaultDriver() === 'admin' ? 'admin.auth.lock' : 'auth.lock'
            );
        }

        return $next($request);
    }

    protected function shouldPassThrough(Request $request)
    {
        foreach ($this->except as $except) {
            $appends = Auth::getDefaultDriver() === 'admin' ? 'admin' : '';
            $except = trim("{$appends}{$except}", '/');

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
