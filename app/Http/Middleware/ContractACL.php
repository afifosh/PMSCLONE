<?php

namespace App\Http\Middleware;

use App\Models\Contract;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContractACL
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $contract = $request->route('contract');

    if (!$contract || Contract::validAccessibleByAdmin(auth()->id())->where('id', $contract->id ?? $contract)->doesntExist())
    {
      // if expects json
      if ($request->expectsJson()) {
        return response()->json(['message' => 'Unauthorized'], 401);
      }

      // if expects html
      abort(401, 'Unauthorized');
    }

    return $next($request);
  }
}
