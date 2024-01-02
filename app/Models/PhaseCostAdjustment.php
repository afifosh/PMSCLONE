<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseCostAdjustment extends Model
{
  use HasFactory;

  protected $fillable = [
    'phase_id',
    'amount',
    'description',
  ];

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = $value * 1000;
  }

  /**
   * The phase to which this adjustment applies.
   */
  public function phase()
  {
    return $this->belongsTo(ContractPhase::class);
  }
}