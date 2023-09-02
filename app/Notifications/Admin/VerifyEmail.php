<?php

namespace App\Notifications\Admin;

use App\Mail\CommonMail;
use App\Models\EmailTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends VerifyEmailBase implements ShouldQueue
{
  use Queueable;

  protected function verificationUrl($notifiable)
  {
    if (static::$createUrlCallback) {
      return call_user_func(static::$createUrlCallback, $notifiable);
    }

    return URL::temporarySignedRoute(
      'admin.verification.verify',
      Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
      [
        'id' => $notifiable->getKey(),
        'hash' => sha1($notifiable->getEmailForVerification()),
      ]
    );
  }

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
      '{verification_link}' => $verificationUrl,
    ];

    $template->langTemplates[0]->content = replaceStrVariables($template->langTemplates[0]->content, $strTemp);

    return (new CommonMail($notifiable, $template))->to($notifiable->email);
  }
}
