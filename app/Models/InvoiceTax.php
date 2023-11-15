<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTax extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'invoice_item_id',
    'tax_id',
    'amount',
    'calculated_amount',
    'manual_amount',
    'is_simple_tax',
    'is_authority_tax',
    'pay_on_behalf'
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function invoiceItem()
  {
    return $this->belongsTo(InvoiceItem::class);
  }

  public function tax()
  {
    return $this->belongsTo(InvoiceConfig::class, 'tax_id');
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
