<?php

namespace Modules\Chat\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Chat\Models\Group;
use Modules\Chat\Models\User;

class MentionedInChatNotification extends Notification implements ShouldQueue
{
  use Queueable;

  protected $group;
  protected $mentionedBy;
  protected $actionUrl;
  /**
   * Create a new notification instance.
   */
  public function __construct(Group $group, $mentionedBy)
  {
    $this->group = $group;
    $this->mentionedBy = $mentionedBy;
    $this->actionUrl = $this->getActionUrl();
  }

  public function getActionUrl()
  {
    if($this->group->project_id){
      return route('admin.chat.project.conversations', ['conversationId' => $this->group->id]);
    }else{
      return route('admin.chat.conversations', ['conversationId' => $this->group->id]);
    }
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
  public function toMail(object $notifiable): MailMessage
  {
    return (new MailMessage)
      ->line('You have been mentioned in a chat by ' . $this->mentionedBy->name)
      ->action('Open Chat', $this->actionUrl)
      ->line('Thank you for using our application!');
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    return [
      //
    ];
  }
}
