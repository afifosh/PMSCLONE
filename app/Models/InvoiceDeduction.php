<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InvoiceDeduction extends Model
{
  use HasFactory;

  protected $fillable = [
    'deductible_type',
    'deductible_id',
    'downpayment_id',
    'dp_rate_id',
    'is_percentage',
    'amount',
    'manual_amount',
    'percentage',
    'is_before_tax',
    'calculation_source'
  ];

  protected $casts = [
    'is_percentage' => 'boolean',
    'is_before_tax' => 'boolean',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  /**
   * Deductible Morph (Invoice or InvoiceItem)
   *
   * @return \Illuminate\Database\Eloquent\Relations\MorphTo
   */
  public function deductible(): MorphTo
  {
    return $this->morphTo();
  }

  /**
   * Downpayment (Downpayment Invoice from which this deduction is created)
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function downpayment(): BelongsTo
  {
    return $this->belongsTo(Invoice::class, 'downpayment_id');
  }

  /**
   * Downpayment Rate (Downpayment Rate from which this deduction is created)
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function dpRate(): BelongsTo
  {
    return $this->belongsTo(InvoiceConfig::class, 'dp_rate_id');
  }

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = moneyToInt($value);
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
