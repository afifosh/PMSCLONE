<?php

namespace App\Events\Admin;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectMilestoneUpdated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $project;
  public $modifiedTab;

  /**
   * Create a new event instance.
   */
  public function __construct($project, $modifiedTab, $message = false)
  {
    $this->project = $project;
    $this->modifiedTab = $modifiedTab;

    if ($message) {
      $this->sendMessage($message);
    }
  }

  public function sendMessage($message)
    {
        $this->project->sendMessageInChat($message);
    }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return array<int, \Illuminate\Broadcasting\Channel>
   */
  public function broadcastOn(): array
    {
        return [
            new PrivateChannel('projects.' . $this->project->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'project-milestone-updated';
    }
}
