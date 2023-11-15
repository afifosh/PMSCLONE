<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorityInvoice extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'is_summary_tax',
    'subtotal',
    'total_tax',
    'total',
    'rounding_amount',
    'paid_amount',
    'downpayment_amount',
    'discount_type',
    'discount_percentage',
    'discount_amount',
    'adjustment_description',
    'adjustment_amount',
    'retention_id',
    'retention_name',
    'retention_percentage',
    'retention_amount',
    'retention_released_at',
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function setSubtotalAttribute($value)
  {
    $this->attributes['subtotal'] = moneyToInt($value);
  }

  public function getTotalAttribute($value)
  {
    return ($value / 1000) + $this->rounding_amount;
  }

  public function setTotalAttribute($value)
  {
    $this->attributes['total'] = moneyToInt($value);
  }

  public function getPaidAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setPaidAmountAttribute($value)
  {
    $this->attributes['paid_amount'] = moneyToInt($value);
  }

  public function getTotalTaxAttribute($value)
  {
    return $value / 1000;
  }

  public function setTotalTaxAttribute($value)
  {
    $this->attributes['total_tax'] = moneyToInt($value);
  }

  public function getDiscountAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setDiscountAmountAttribute($value)
  {
    $this->attributes['discount_amount'] = moneyToInt($value);
  }

  public function getAdjustmentAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAdjustmentAmountAttribute($value)
  {
    $this->attributes['adjustment_amount'] = moneyToInt($value);
  }

  public function getRetentionAmountAttribute($value)
  {
    if ($this->retention_released_at) {
      return 0;
    }

    return $value / 1000;
  }

  public function setRetentionAmountAttribute($value)
  {
    $this->attributes['retention_amount'] = moneyToInt($value);
  }

  public function getDownpaymentAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setDownpaymentAmountAttribute($value)
  {
    $this->attributes['downpayment_amount'] = moneyToInt($value);
  }

  public function getRoundingAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setRoundingAmountAttribute($value)
  {
    $this->attributes['rounding_amount'] = moneyToInt($value);
  }
}
