<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAndCompanyMustActive
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
    if (auth('web')->check()) {
      if (auth()->user()->status != 'active' || auth()->user()->company->status != 'active') {
        auth()->logout();
        session()->flush();
        session()->flash('inactive-user', 'Your Account Is not active. Please Contact Support');
        return redirect()->route('login');
      }
    }
    return $next($request);
  }
}
