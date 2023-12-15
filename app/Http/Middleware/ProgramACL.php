<?php

namespace App\Http\Middleware;

use App\Models\Program;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgramACL
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $program = $request->route('program');
    if(@$program->id) {
      $program = $program->id;
    }

    if(Program::validAccessibleByAdmin(auth()->id())->where('id', $program)->doesntExist()) {
      // if expects json
      if (request()->expectsJson()) {
        return response()->json(['message' => 'Unauthorized'], 401);
      }

      // if expects html
      abort(401, 'Unauthorized');
    }
    return $next($request);
  }
}
