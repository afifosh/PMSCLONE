<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommonMail extends Mailable
{
  use Queueable, SerializesModels;

  public $notifiable;
  public $template;
  /**
   * Create a new message instance.
   */
  public function __construct($notifiable, $template)
  {
    $this->notifiable = $notifiable;
    $this->template = $template;
  }

  public function build()
  {
    return $this->from(config('mail.from.address'), $this->template->from)->markdown('emails.admin.common_email_template')->subject($this->template->langTemplates[0]->subject)->with('content', $this->template->langTemplates[0]->content);
  }
}
