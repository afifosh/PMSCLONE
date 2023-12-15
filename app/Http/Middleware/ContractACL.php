<?php

namespace App\Http\Middleware;

use App\Models\Contract;
use App\Models\Invoice;
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

    if (@$contract->id) {
      $contract = $contract->id;
    }

    // if contract is not set, try to get invoice
    if (!$contract && $request->route('invoice')) {
      $invoice = $request->route('invoice');
    }

    if (@$invoice->id) {
      $invoice = $invoice->id;
    }

    if (@$invoice && Invoice::validAccessibleByAdmin(auth()->id())->where('id', $invoice)->doesntExist()) {
      $this->deny();
    } elseif ($contract && Contract::validAccessibleByAdmin(auth()->id())->where('id', $contract->id ?? $contract)->doesntExist()) {
      $this->deny();
    } elseif (!$contract && !$invoice) {
      $this->deny();
    }

    return $next($request);
  }

  private function deny()
  {
    // if expects json
    if (request()->expectsJson()) {
      return response()->json(['message' => 'Unauthorized'], 401);
    }

    // if expects html
    abort(401, 'Unauthorized');
  }
}
