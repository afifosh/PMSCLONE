<?php

namespace App\Notifications\Admin\Contract;

use App\Mail\Admin\ContractTerminationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ContractTerminationNotification extends Notification implements ShouldQueue
{
  use Queueable;

  public $contract;
  public $isImmediate;

  /**
   * Create a new notification instance.
   */
  public function __construct($contract, $isImmediate)
  {
    $this->contract = $contract;
    $this->isImmediate = $isImmediate;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    return ['database', 'broadcast', 'mail'];
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(object $notifiable)
  {
    return (new ContractTerminationMail($notifiable, $this->contract))->to($notifiable->email);
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    return [
      'data' => [
        'title' => 'Contract Terminated',
        'description' => 'Contract : ' . $this->contract->subject . ' is terminated',
        'action_url' => route('admin.contracts.show', $this->contract->id),
        'view' => 'admin.notifications.task-reminder',
      ]
    ];
  }

  public function toBroadcast(object $notifiable): BroadcastMessage
  {
    return new BroadcastMessage([
      'type' => 'contract-terminated',
    ]);
  }
}
