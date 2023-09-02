<?php

namespace App\Notifications\User;

use App\Mail\CommonMail;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail;

class UserVerifyEmailQueued extends VerifyEmail implements ShouldQueue
{
  use Queueable;

  // Nothing else needs to go here unless you want to customize
  // the notification in any way.

  public function toMail($notifiable)
  {
    $verificationUrl = $this->verificationUrl($notifiable);

    if (static::$toMailCallback) {
      return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
    }

    $template = EmailTemplate::where('slug', 'verify_email')->with(['langTemplates' => function ($query) use ($notifiable) {
      $query->where('lang', $notifiable->lang ? $notifiable->lang : 'en');
    }])->first();

    $strTemp = [
      '{user_name}' => $notifiable->first_name . ' ' . $notifiable->last_name,
      '{user_email}' => $notifiable->email,
      '{verification_url}' => $verificationUrl,
    ];

    $template->langTemplates[0]->content = replaceStrVariables($template->langTemplates[0]->content, $strTemp);

    return (new CommonMail($notifiable, $template))->to($notifiable->email);
  }
}
