<?php

namespace App\Http\Responses;

use App\Notifications\Auth\AuthLogNotification;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Http\Responses\LoginResponse as ResponsesLoginResponse;

class LoginResponse extends ResponsesLoginResponse implements LoginResponseContract
{

  /**
   * @param  $request
   * @return mixed
   */
  public function toResponse($request)
  {
    /**
     * @var \App\Models\User
     */
    $user = Auth::guard(config('fortify.guard'))->user();

    if ($user->findIfLoginUpdated()) {
      $user->notify(
        new AuthLogNotification(
          $user->authentications,
          $user->lastLoginAgent(),
          $user->lastLoginLocation()
        )
      );
    }

    return parent::toResponse($request);
  }
}
