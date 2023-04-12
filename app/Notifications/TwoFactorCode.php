<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\HtmlString;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorCode extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    //phpcs:ignore
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
        //dd($notifiable);
        $twoFaCode = '<p style="color:#1d82f5"><strong>' . $notifiable->two_factor_code . '</strong></p>';

        return (new MailMessage)
            ->greeting('Hello' . ' ' . ucwords($notifiable->first_name) . ',')
            ->line('Your two-factor authentication code is ')
            ->line(new HtmlString($twoFaCode))
            ->line('The code will expire in 10 minutes')
            ->line('If you have not tried to login, ignore this message.')
            ->line('Thank you for using our application!');
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function getTwoFactorCode($notifiable): ?string
    {
        if(!$notifiable->two_factor_secret){
            return null;
        }
        $currentOTP = app(Google2FA::class)->getCurrentOtp(decrypt($notifiable->two_factor_secret));
        return   $currentOTP;
    }
}