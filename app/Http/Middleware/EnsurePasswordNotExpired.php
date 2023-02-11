<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class EnsurePasswordNotExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();
        $password_changed_at = new Carbon(($user->password_changed_at) ? $user->password_changed_at : $user->created_at);

        $redirects = [
            'admin' => redirect()->route('admin.password.expired.'),
            'user' => redirect()->route('password.expired.'),
        ];

        if (Carbon::now()->diffInDays($password_changed_at) >= 30) {
            return $redirects[$role];
        }

        return $next($request);
    }
}
