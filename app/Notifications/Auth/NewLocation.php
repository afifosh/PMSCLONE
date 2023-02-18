<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class NewLocation extends Notification implements ShouldQueue
{
    use Queueable;

    public AuthenticationLog $authenticationLog;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(AuthenticationLog $authenticationLog)
    {
        $this->authenticationLog = $authenticationLog;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(__('Your :app account logged in from a new location.', ['app' => config('app.name')]))
            ->markdown('authentication-log::emails.new-location', [
                'account' => $notifiable,
                'time' => $this->authenticationLog->login_at,
                'ipAddress' => $this->authenticationLog->ip_address,
                'browser' => $this->authenticationLog->user_agent,
                'location' => $this->authenticationLog->location,
            ]);
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
