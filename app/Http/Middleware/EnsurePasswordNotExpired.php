<?php

namespace App\Http\Middleware;

use App\Services\Core\Setting\General\SettingService;
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
        $passwordChangedAt = new Carbon(($user->password_changed_at) ? $user->password_changed_at : null );

        $redirects = [
            'admin' => redirect()->route('admin.password.expired.'),
            'web' => redirect()->route('password.expired.'),
        ];

        $security = app(SettingService::class)->getFormattedSettings('security');
        $passwordExpiresInDays = $security['password_expire_days'] ?? config('auth.password_expire_days');

        if (!$user->password_changed_at || Carbon::now()->diffInDays($passwordChangedAt) >= $passwordExpiresInDays) {
            return $redirects[$guard];
        }

        return $next($request);
    }
}
