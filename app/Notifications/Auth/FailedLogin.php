<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Rappasoft\LaravelAuthenticationLog\Notifications\FailedLogin as NotificationsFailedLogin;

class FailedLogin extends NotificationsFailedLogin
{
    use Queueable;

    public function via($notifiable)
    {
        return [
            'database', 'mail'
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'account' => $notifiable,
            'time' => $this->authenticationLog->login_at,
            'ipAddress' => $this->authenticationLog->ip_address,
            'browser' => $this->authenticationLog->user_agent,
            'location' => $this->authenticationLog->location,
        ];
    }
}
