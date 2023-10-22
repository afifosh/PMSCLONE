<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractStage extends BaseModel
{
  use HasFactory;

  protected $fillable = [
    'name'
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  protected $appends = ['status', 'stage_amount', 'start_date', 'due_date'];

  public function getStatusAttribute()
  {
    // if($this->due_date->isPast()) return 'Expired';
    // elseif($this->start_date->isFuture()) return 'Not started';
    // elseif(now() > $this->due_date->subMonth()) return 'About To Expire';
    // elseif(now() >= $this->start_date) return 'Active';
    if ($this->due_date === null && $this->start_date === null)
      return '';

    if ($this->due_date == null && $this->start_date) {
      if ($this->start_date->isSameDay(today())) {
        return "Active";
      } elseif ($this->start_date->isFuture()) {
        return "Not Started";
      } else {
        return "Expired";
      }
    } else {
      if ($this->due_date->isPast()) {
        return "Expired";
      } elseif ($this->start_date->isFuture()) {
        return "Not Started";
      } elseif (now()->diffInDays($this->due_date) <= 30) {
        return "About To Expire";
      } else {
        return "Active";
      }
    }
  }

  public function getStageAmountAttribute()
  {
    return $this->phases->sum('estimated_cost');
  }

  public function getStartDateAttribute()
  {
    return $this->phases->min('start_date');
  }

  public function getDueDateAttribute()
  {
    return $this->phases->max('due_date');
  }

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function phases()
  {
    return $this->hasMany(ContractPhase::class, 'stage_id');
  }
}
