<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminWelcomeMail extends Mailable
{
  use Queueable, SerializesModels;
  public $user;
  public $data;
  public $template;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($notifiable, array $data)
  {
    $this->user = $notifiable;
    $this->data = $data;
    $this->template = EmailTemplate::where('slug', 'new_user')->with(['langTemplates' => function ($query) use ($notifiable) {
      $query->where('lang', $notifiable->lang ? $notifiable->lang : 'en');
    }])->first();

    $this->template->langTemplates[0]->content = $this->replaceVariable($this->template->langTemplates[0]->content, $data + $this->user->toArray());
  }

  public function build()
  {
    return $this->from(config('mail.from.address'), $this->template->from)->markdown('emails.admin.common_email_template')->subject($this->template->langTemplates[0]->subject)->with('content', $this->template->langTemplates[0]->content);
  }

  public function replaceVariable($content, $data)
  {
    $content = str_replace(
      [
        '{first_name}',
        '{last_name}',
        '{email}',
        '{password}',
        '{app_name}',
        '{app_url}'
      ],
      [
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['password'],
        config('app.name'),
        config('app.url')
      ],
      $content
    );
    return $content;
  }
}
