<?php

namespace App\Http\Responses;

use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse as FailedTwoFactorLoginResponseContract;
use Laravel\Fortify\Http\Responses\FailedTwoFactorLoginResponse as ResponsesFailedTwoFactorLoginResponse;

class FailedTwoFactorLoginResponse extends ResponsesFailedTwoFactorLoginResponse implements FailedTwoFactorLoginResponseContract
{
  /**
   * Create a response object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function toResponse($request)
  {
    if (config('fortify.guard') === 'admin') {
      [$key, $message] = $request->filled('recovery_code')
        ? ['recovery_code', __('The provided two factor recovery code was invalid.')]
        : ['code', __('The provided two factor authentication code was invalid.')];

      if ($request->wantsJson()) {
        throw ValidationException::withMessages([
          $key => [$message],
        ]);
      }

      return back()->withErrors([$key => $message]);
    }

    return parent::toResponse($request);
  }
}
