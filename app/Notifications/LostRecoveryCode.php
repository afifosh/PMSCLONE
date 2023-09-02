<?php

namespace App\Notifications;

use App\Mail\CommonMail;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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

      $template = EmailTemplate::where('slug', 'lost_recovery_code')->with(['langTemplates' => function ($query) use ($notifiable) {
        $query->where('lang', $notifiable->lang ? $notifiable->lang : 'en');
      }])->first();

      $strTemp = [
        '{user_name}' => $notifiable->first_name . ' ' . $notifiable->last_name,
        '{user_email}' => $notifiable->email,
      ];

      $template->langTemplates[0]->content = replaceStrVariables($template->langTemplates[0]->content, $strTemp);

      // Generate a file with the recovery codes
      $codes = json_decode(decrypt($notifiable->two_factor_recovery_codes), true);
      $fileContent = implode(PHP_EOL, $codes);
      $fileName = '2fa-recovery-codes.txt';

      return (new CommonMail($notifiable, $template))->to($notifiable->email)->attachData($fileContent, $fileName, ['mime' => 'text/plain']);
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
