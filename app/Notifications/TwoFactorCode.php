<?php

namespace App\Notifications;

use App\Mail\CommonMail;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
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

      $template = EmailTemplate::where('slug', 'two_factor_code')->with(['langTemplates' => function ($query) use ($notifiable) {
        $query->where('lang', $notifiable->lang ? $notifiable->lang : 'en');
      }])->first();

      $strTemp = [
        '{user_name}' => $notifiable->first_name . ' ' . $notifiable->last_name,
        '{user_email}' => $notifiable->email,
        '{two_factor_code}' => $notifiable->two_factor_code,
      ];

      $template->langTemplates[0]->content = replaceStrVariables($template->langTemplates[0]->content, $strTemp);

      return (new CommonMail($notifiable, $template))->to($notifiable->email);
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
