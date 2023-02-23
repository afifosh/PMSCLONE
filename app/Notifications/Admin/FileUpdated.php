<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileUpdated extends Notification
{
    use Queueable;

    protected $file;
    protected $data;

    public function __construct($file, $data)
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
            'title' => $this->data['title'],
            'data' => [
                'image' => $this->data['image'],
                'description' => isset($data['desc']) ? isset($data['desc']) : $this->data['user']->full_name . ' updated a file ( '.$this->file->title.' )',
                'action_url' => isset($data['url']) ? $data['url'] : route('admin.draft-rfps.files.index', $this->file->rfp_id),
                'view' => 'admin.notifications.file-updated',
            ]
        ];
    }
}
