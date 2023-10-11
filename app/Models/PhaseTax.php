<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseTax extends Model
{
  use HasFactory;

  protected $fillable = [
    'contract_phase_id',
    'tax_id',
    'amount',
    'type',
  ];

  public function contractPhase()
  {
    return $this->belongsTo(ContractPhase::class);
  }

  public function tax()
  {
    return $this->belongsTo(Tax::class);
  }

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = moneyToInt($value);
  }
}
