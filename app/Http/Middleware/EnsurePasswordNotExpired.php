<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordNotExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::getDefaultDriver();
        $user = $request->user();
        $password_changed_at = new Carbon(($user->password_changed_at) ? $user->password_changed_at : null );
        $app_settings = AppSetting::first();

        $redirects = [
            'admin' => redirect()->route('admin.password.expired.'),
            'web' => redirect()->route('password.expired.'),
        ];

        $password_expire_days =
            isset($app_settings->password_expire_days) && ! is_null($app_settings->password_expire_days)
            ? $app_settings->password_expire_days
            : config('auth.password_expire_days');

        if (!$user->password_changed_at || Carbon::now()->diffInDays($password_changed_at) >= $password_expire_days) {
            return $redirects[$guard];
        }

        return $next($request);
    }
}
