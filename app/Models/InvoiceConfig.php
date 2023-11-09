<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceConfig extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'type',
    'amount',
    'status',
    'config_type'
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  /**
   * Scope Active Retention
   */
  public function scopeActiveRetentiones($q)
  {
    $q->where('config_type', 'Retention')->activeOnly();
  }

  /**
   * Scope Active Tax
   */
  public function scopeActiveTaxes($q)
  {
    $q->where('config_type', 'Tax')->activeOnly();
  }

  /**
   * Scope Active only
   */
  public function scopeActiveOnly($q)
  {
    $q->where('status', 'Active');
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = moneyToInt($value);
  }
}
