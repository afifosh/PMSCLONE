<?php

namespace App\Notifications\Admin;

use App\Models\FileShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class FileShared extends Notification implements ShouldQueue
{
    use Queueable;

    protected $file_share;
    protected $data;

    public function __construct(FileShare $file_share, $data = [])
    {
      $this->file_share = $file_share;
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
        return ['database', 'broadcast'];
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
            'title' => 'File Shared with',
            'data' => [
                'image' => $this->file_share->sharedBy->avatar,
                'description' => $this->file_share->sharedBy->full_name . ' shared a file ( '.$this->file_share->file->title.' )',
                'shared_with_id' => $this->file_share->user->id,
                'shared_with_name' => $this->file_share->user->full_name,
                'action_url' => isset($data['url']) ? $data['url'] : route('admin.draft-rfps.files.index', $this->file_share->file->rfp_id),
                'view' => 'admin.notifications.file-shared',
            ]
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
          'type' => 'file-shared'
        ]);
    }
}
