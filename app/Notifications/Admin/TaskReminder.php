<?php

namespace App\Notifications\Admin;

use App\Mail\CommonMail;
use App\Models\EmailTemplate;
use App\Models\TaskReminder as ModelsTaskReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
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
    public function toMail($notifiable)
    {
      $template = EmailTemplate::where('slug', 'contract_task_reminder')->with(['langTemplates' => function ($query) use ($notifiable) {
        $query->where('lang', $notifiable->lang ? $notifiable->lang : 'en');
      }])->first();

      $strTemp = [
        '{user_name}' => $notifiable->first_name . ' ' . $notifiable->last_name,
        '{user_email}' => $notifiable->email,
        '{task_subject}' => @$this->taskReminder->task->subject ?? 'N/A',
        '{reminder_description}' => @$this->taskReminder->description ?? 'N/A',
        '{reminder_set_by_name}' => @$this->taskReminder->sender->name ?? 'N/A',
        '{reminder_set_by_email}' => @$this->taskReminder->sender->email ?? 'N/A',
        '{task_view_url}' => route('admin.projects.tasks.index', @$this->taskReminder->task->project_id) ?? 'N/A',
      ];

      $template->langTemplates[0]->content = replaceStrVariables($template->langTemplates[0]->content, $strTemp);

      return (new CommonMail($notifiable, $template))->to($notifiable->email);
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
