<?php

namespace App\Events\Admin;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectTaskUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $projectTask;
    public $modifiedTab;
    /**
     * Create a new event instance.
     */
    public function __construct($projectTask, $modifiedTab, $message = false)
    {
        $this->projectTask = $projectTask;
        $this->modifiedTab = $modifiedTab;

        if ($message) {
            $this->sendMessage($message);
        }
    }

    public function sendMessage($message)
    {
        $this->projectTask->project->sendMessageInChat($message);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('projects.' . $this->projectTask->project_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'project-updated';
    }
}
