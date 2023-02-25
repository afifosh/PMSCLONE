<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FileUploaded extends Notification implements ShouldQueue
{
  use Queueable;

  protected $file;
  protected $data;
  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($file, $data = [])
  {
    $this->file = $file;
    $this->data = $data;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['database'];
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
      'title' => isset($this->data['title']) ? $this->data['title'] : $this->file->uploadedBy->full_name. ' uploaded a file.',
      'data' => [
        'image' => $this->file->uploadedBy->avatar,
        'description' => isset($data['desc']) ? isset($data['desc']) : $this->file->uploadedBy->full_name . ' uploaded a file ( ' . $this->file->title . ' ) against '. $this->file->rfp->name ,
        'action_url' => isset($data['url']) ? $data['url'] : route('admin.draft-rfps.files.index', $this->file->rfp_id),
        'view' => 'admin.notifications.general',
      ]
    ];
  }
}
