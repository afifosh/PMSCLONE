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
    'estimated_cost',
    'description'
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getEstimatedCostAttribute($value)
  {
    return $value / 100;
  }

  public function setEstimatedCostAttribute($value)
  {
    $this->attributes['estimated_cost'] = round($value * 100);
  }

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function phases()
  {
    return $this->hasMany(ContractPhase::class, 'stage_id');
  }

  public function remainingAmount()
  {
    return $this->estimated_cost - $this->phases->sum('estimated_cost');
  }
}
