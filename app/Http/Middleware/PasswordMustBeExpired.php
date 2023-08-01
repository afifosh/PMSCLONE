<?php

namespace App\Http\Middleware;

use App\Services\Core\Setting\SettingService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PasswordMustBeExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $passwordChangedAt = new Carbon(($user->password_changed_at) ? $user->password_changed_at : null );
        $passwordExpiresInDays = config('auth.password_expire_days');

        if (!$user->password_changed_at || Carbon::now()->diffInDays($passwordChangedAt) >= $passwordExpiresInDays) {
           return $next($request);
        }

        return back();
    }
}
