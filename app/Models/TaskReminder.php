<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskReminder extends Model
{
  use HasFactory;

  protected $fillable = [
    'task_id',
    'recipient_id',
    'sender_id',
    'description',
    'remind_at',
    'can_send_email',
  ];

  protected $casts = [
    'remind_at' => 'datetime:d M, Y',
  ];

  public function task()
  {
    return $this->belongsTo(Task::class);
  }

  public function recipient()
  {
    return $this->belongsTo(Admin::class, 'recipient_id');
  }

  public function sender()
  {
    return $this->belongsTo(Admin::class, 'sender_id');
  }
}
