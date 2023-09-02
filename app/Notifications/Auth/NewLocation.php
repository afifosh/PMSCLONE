<?php

namespace App\Notifications\Auth;

use App\Mail\CommonMail;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
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
    return ['database', 'broadcast', 'mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    $template = EmailTemplate::where('slug', 'new_location_login')->with(['langTemplates' => function ($query) use ($notifiable) {
      $query->where('lang', $notifiable->lang ? $notifiable->lang : 'en');
    }])->first();

    $strTemp = [
      '{user_name}' => $notifiable->first_name . ' ' . $notifiable->last_name,
      '{user_email}' => $notifiable->email,
      '{time}' => @$this->authenticationLog->login_at->toCookieString(),
      '{ip_address}' => @$this->authenticationLog->ip_address,
      '{browser}' => @$this->authenticationLog->user_agent,
      '{location_city}' => @$this->authenticationLog->location['city'] ?? 'Unknown',
      '{location_state}' => @$this->authenticationLog->location['state'] ?? 'Unknown',
    ];

    $template->langTemplates[0]->content = replaceStrVariables($template->langTemplates[0]->content, $strTemp);

    return (new CommonMail($notifiable, $template))->to($notifiable->email);
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

  public function toBroadcast($notifiable)
  {
    return new BroadcastMessage([
      'type' => 'new-device'
    ]);
  }
}
