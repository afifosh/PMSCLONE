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
        $message = auth()->user()->status != 'active' ? 'Your Account Is not active. Please Contact Support' : 'Your Company Is not active. Please Contact Support';
        auth()->logout();
        session()->flush();
        session()->flash('inactive-user', $message);
        return redirect()->route('login');
      }
    }elseif(auth('admin')->check()){
      if (auth('admin')->user()->status != 'active' || ( auth('admin')->user()->designation_id && @auth()->user()->designation()->department->company->status != 'active')) {
        $message = auth('admin')->user()->status != 'active' ? 'Your Account Is not active. Please Contact Support' : 'Your Organization Is not active. Please Contact Support';
        auth('admin')->logout();
        session()->flush();
        session()->flash('inactive-user', $message);
        return redirect()->route('admin.login');
      }
    }
    return $next($request);
  }
}
