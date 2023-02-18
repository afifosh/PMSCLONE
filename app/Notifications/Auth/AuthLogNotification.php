<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;

class AuthLogNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $auth_log;
    public $auth_device;
    public $location;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($auth_log, $auth_agent, $location)
    {
        $this->auth_log = $auth_log;
        $this->location = $location;
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
            'device' => $this->auth_device,
            'city' => $this->location['city'],
            'country' => $this->location['country'],
        ];
    }
}
