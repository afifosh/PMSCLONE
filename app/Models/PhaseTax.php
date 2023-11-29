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
    'calculated_amount',
    'manual_amount',
    'category'
  ];

  public function contractPhase()
  {
    return $this->belongsTo(ContractPhase::class);
  }

  public function tax()
  {
    return $this->belongsTo(InvoiceConfig::class);
  }

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = moneyToInt($value);
  }

  public function getCalculatedAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setCalculatedAmountAttribute($value)
  {
    $this->attributes['calculated_amount'] = moneyToInt($value);
  }

  public function getManualAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setManualAmountAttribute($value)
  {
    $this->attributes['manual_amount'] = moneyToInt($value);
  }
}
