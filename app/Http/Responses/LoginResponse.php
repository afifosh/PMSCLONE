<?php

namespace App\Http\Responses;

use App\Notifications\AuthLogNotification;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Http\Responses\LoginResponse as ResponsesLoginResponse;
use Stevebauman\Location\Facades\Location;

class LoginResponse extends ResponsesLoginResponse implements LoginResponseContract
{

    /**
     * @param  $request
     * @return mixed
     */
    public function toResponse($request)
    {
      $user = Auth::guard(config('fortify.guard'))->user();
      if ($user->checkIfLastLoginDetailsChanged()) {
        $user->notify(new AuthLogNotification($user->authentications, $user->lastLoginAgent(),  $user->lastLoginLocation()));
      }
      return parent::toResponse($request);
    }
}