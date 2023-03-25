<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCompanyIsEditable
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next, bool $triggerNext = false)
  {
    if (auth()->user()->company->isEditable() == false) {
      if ($triggerNext) {
        return response()->json(['success' => true, 'data' => ['event' => 'functionCall', 'function' => 'triggerNext']]);
      }

      return response()->json(['message' => 'Company is not editable'], 403);
    }

    return $next($request);
  }
}
