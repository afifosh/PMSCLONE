<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;

class AuthLogNotification extends Notification
{
    use Queueable;
    public $auth_log;
    public $auth_device;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($auth_log, $auth_agent)
    {
        $this->auth_log = $auth_log;
        $agent = new Agent();
    
        $agent->setUserAgent($auth_agent);
    
        $this->auth_device = $agent->device();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return '';
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
            'auth_device' => $this->auth_device,
        ];
    }
}
