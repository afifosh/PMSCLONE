<?php

namespace App\Events\Admin\Contract;

use App\Models\Contract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractUpdated implements ShouldBroadcast
{
  use InteractsWithSockets, SerializesModels;

  /**
   * Updated Contract
   * App\Models\Contract $contract
   */
  public $contract;

  /**
   * Modified Tab
   * string $modifiedTab
   */
  public $modifiedTab;
  /**
   * Create a new event instance.
   */
  public function __construct(Contract $contract, $modifiedTab)
  {
    $this->contract = $contract;
    $this->modifiedTab = $modifiedTab;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return array<int, \Illuminate\Broadcasting\Channel>
   */
  public function broadcastOn(): array
  {
    return [
      new PresenceChannel('contracts.' . $this->contract->id),
    ];
  }

  public function broadcastAs(): string
  {
    return 'contract-updated';
  }
}
