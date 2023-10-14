<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDownpayment extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'downpayment_id',
    'is_percentage',
    'amount',
    'percentage',
    'is_after_tax'
  ];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function downpaymentInvoice()
  {
    return $this->belongsTo(Invoice::class, 'downpayment_id');
  }

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = moneyToInt($value);
  }

  public function getPercentageAttribute($value)
  {
    return $value / 1000;
  }

  public function setPercentageAttribute($value)
  {
    $this->attributes['percentage'] = moneyToInt($value);
  }
}
