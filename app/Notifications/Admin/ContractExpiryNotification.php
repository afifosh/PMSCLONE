<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ContractExpiryMail;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ContractExpiryNotification extends Notification implements ShouldQueue
{
  use Queueable;

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
    return ['database', 'broadcast', 'mail'];
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(object $notifiable)
  {
    return (new ContractExpiryMail($notifiable, $this->contract))->to($notifiable->email);
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
              'title' => 'Contract Expiring Soon',
              'description' => 'Contract : ' . $this->contract->subject . ' is expiring on ' . formatDateTime($this->contract->end_date),
              'action_url' => route('admin.contracts.show', $this->contract->id),
              'view' => 'admin.notifications.task-reminder',
            ]
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'contract-expiry',
        ]);
    }
}
