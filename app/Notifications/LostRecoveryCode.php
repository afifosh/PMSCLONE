<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LostRecoveryCode extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $codes = json_decode(decrypt($notifiable->two_factor_recovery_codes), true);


        // Generate a file with the recovery codes
        $fileContent = implode(PHP_EOL, $codes);
        $fileName = '2fa-recovery-codes.txt';
    

    
        return (new MailMessage)
                    ->subject('Two-Factor Recovery Code')
                    ->greeting('Hello' . ' ' . ucwords($notifiable->first_name) . ',')
                    ->line('Here are your Two-Factor Recovery Code:')
                    ->attachData($fileContent, $fileName, ['mime' => 'text/plain'])
                    ->line('These codes can be used to recover your account if you lose your two-factor authentication device.')
                    ->line('Please keep them in a secure place.')
                    ->line('Thank you for using our application!');
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
            //
        ];
    }
}
