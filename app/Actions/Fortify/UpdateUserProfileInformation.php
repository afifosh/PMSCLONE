<?php

namespace App\Actions\Fortify;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update($user, array $input){
      if(config('fortify.guard') == 'admin'){
        $this->updateAdmin($user, $input);
      }
      else{
        $this->updateUser($user, $input);
      }
    }
    public function updateUser(User $user, array $input): void
    {
        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill($input)->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill(['email_verified_at' => null] + $input)->save();

        $user->sendEmailVerificationNotification();
    }

    public function updateAdmin(Admin $user, array $input): void
    {
        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedAdmin($user, $input);
        } else {
            $user->forceFill($input)->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedAdmin(Admin $user, array $input): void
    {
        $user->forceFill(['email_verified_at' => null] + $input)->save();

        $user->sendEmailVerificationNotification();
    }
}
