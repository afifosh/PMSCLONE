<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Models\Group;

/**
 * Class GroupEvent
 */
class GroupEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    /**
     * Create a new event instance.
     *
     * @param  array  $data
     */
    public function __construct($data)
    {
        $this->data = $data;

        $this->setProjectId();
    }

    /**
     * Set project id
     */
    private function setProjectId()
    {
        $this->data['project_id'] = $this->data['group']['project_id'] ?? Group::find($this->data['group']['id'])->project_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('group.'.$this->data['group']['id']);
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->data;
    }
}
