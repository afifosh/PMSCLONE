<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractStage extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'type',
    'start_date',
    'due_date',
    'stage_amount',
    'remaining_amount',
    'description'
  ];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  protected $appends = ['status'];

  public function getStatusAttribute()
  {
    // if($this->due_date->isPast()) return 'Expired';
    // elseif($this->start_date->isFuture()) return 'Not started';
    // elseif(now() > $this->due_date->subMonth()) return 'About To Expire';
    // elseif(now() >= $this->start_date) return 'Active';
    $value = $this->getRawOriginal('status');

    if ($value == 'Terminated' || $value == 'Paused' || $value == 'Draft') return $value;

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

  public function getStageAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setStageAmountAttribute($value)
  {
    $this->attributes['stage_amount'] = round($value * 1000);
  }

  public function getRemainingAmountAttribute()
  {
      return $this->calculateRemainingAmount();
  }

  public function setRemainingAmountAttribute($value)
  {
    $this->attributes['remaining_amount'] = round($value * 1000);
  }

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function phases()
  {
    return $this->hasMany(ContractPhase::class, 'stage_id');
  }

  public function calculateRemainingAmount()
  {
      // Get total phases amount in the same format as it's stored in the database (multiplied by 1000)
      $totalPhasesAmount = $this->phases->sum(function($phase) {
          return $phase->getOriginal('estimated_cost');
      });
      $totalPhasesAmount = round($totalPhasesAmount, 0);
      // Return the calculated remaining amount divided by 1000 to match your getter's format
      return ($this->getOriginal('stage_amount') - $totalPhasesAmount);
  }
  
  
}
