<?php

namespace App\Notifications\Admin;

use App\Models\Task;
use App\Models\TaskReminder as ModelsTaskReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $taskReminder;

    /**
     * Create a new notification instance.
     */
    public function __construct(ModelsTaskReminder $taskReminder)
    {
        $this->taskReminder = $taskReminder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($this->taskReminder->notify_by_email) {
            return ['database', 'broadcast' ,'mail'];
        }
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Task Reminder')
                    ->greeting('Hello' . ' ' . ucwords($notifiable->first_name) . ',')
                    ->line('This is a reminder for the task: ' . $this->taskReminder->task->subject)
                    ->line('Description: ' . $this->taskReminder->description)
                    ->line('Set By: ' . $this->taskReminder->sender->name)
                    ->action('View Task', route('admin.projects.tasks.index', $this->taskReminder->task->project_id));
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
              'title' => 'Task Reminder',
              'description' => 'This is a reminder for the task: ' . $this->taskReminder->task->subject,
              'action_url' => route('admin.projects.tasks.index', $this->taskReminder->task->project_id),
              'view' => 'admin.notifications.task-reminder',
            ]
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'task-reminder',
        ]);
    }
}
