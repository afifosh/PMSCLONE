<?php

namespace Modules\Chat\Repositories;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Modules\Chat\Mail\MarkdownMail;
use Modules\Chat\Models\User;
use Exception;

/**
 * Class AccountRepository.
 */
class AccountRepository
{
    /**
     * @param $userId
     * @return string
     */
    public function generateUserActivationToken($userId)
    {
        $activation_code = uniqid();

        /** @var User $user */
        $user = User::find($userId);
        $user->activation_code = $activation_code;
        $user->save();

        $key = $user->id.'|'.$activation_code;
        $code = Crypt::encrypt($key);

        return $code;
    }

    /**
     * @param  string  $username
     * @param  string  $email
     * @param  string  $activateCode
     * @param  string  $url
     *
     * @throws Exception
     */
    public function sendConfirmEmail($username, $email, $activateCode, $url = '')
    {
        $data['link'] = ($url != '') ? $url.'/activate?token='.$activateCode : URL::to('/activate?token='.$activateCode);
        $data['username'] = $username;

        try {
            Mail::to($email)
                ->send(new MarkdownMail('auth.emails.account_verification', 'Activate your account', $data));
        } catch (Exception $e) {
            throw new Exception('Account created, but unable to send email');
        }
    }
}
