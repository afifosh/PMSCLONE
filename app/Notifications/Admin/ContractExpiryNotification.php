<?php

namespace App\Notifications\Admin;

// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ContractExpiryMail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractExpiryNotification extends Notification
{
  // use Queueable;

  public $contract;

  /**
   * Create a new notification instance.
   */
  public function __construct($contract)
  {
    $this->contract = $contract;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(object $notifiable)
  {
    return (new ContractExpiryMail($notifiable, $this->contract))->to($notifiable->email);
  }
}
