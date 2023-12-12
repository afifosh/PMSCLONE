<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyContractNotTempered
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next, $model, $key, $modelToCompare = null, $modelKeyToCompare = null): Response
  {
    if($modelToCompare){
      $route_model_id = $request->route($modelToCompare)->{$modelKeyToCompare};
    }
    else{
      $route_model_id = $request->route('contract')->id ?? $request->route('contract');
    }

    if($route_model_id != $request->route($model)->{$key})
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
