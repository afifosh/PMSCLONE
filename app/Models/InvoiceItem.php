<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InvoiceItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'invoiceable_type',
    'invoiceable_id',
    'amount',
    'total_tax_amount',
    'manual_tax_amount',
    'downpayment_id',
    'is_downpayment_percentage',
    'downpayment_amount',
    'downpayment_percentage',
    'manual_downpayment_amount',
    'description',
    'order'
  ];

  protected $casts = [
    // 'amount' => 'integer',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = moneyToInt($value);
  }

  public function getTotalTaxAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setTotalTaxAmountAttribute($value)
  {
    $this->attributes['total_tax_amount'] = moneyToInt($value);
  }

  public function getManualTaxAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setManualTaxAmountAttribute($value)
  {
    $this->attributes['manual_tax_amount'] = moneyToInt($value);
  }

  public function getDownpaymentAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setDownpaymentAmountAttribute($value)
  {
    $this->attributes['downpayment_amount'] = moneyToInt($value);
  }

  public function getManualDownpaymentAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setManualDownpaymentAmountAttribute($value)
  {
    $this->attributes['manual_downpayment_amount'] = moneyToInt($value);
  }

  public function getDownpaymentPercentageAttribute($value)
  {
    return $value / 1000;
  }

  public function setDownpaymentPercentageAttribute($value)
  {
    $this->attributes['downpayment_percentage'] = moneyToInt($value);
  }

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function invoiceable()
  {
    return $this->morphTo();
  }

  public function taxes(): BelongsToMany
  {
    return $this->belongsToMany(Tax::class, 'invoice_taxes')->withPivot('amount', 'type');
  }

  /**
   * pivot table for storing taxes so that updating main tax table doesn't affect old invoices.
   * Tax amount for invoices is calculated from this table.
   */
  public function pivotTaxes()
  {
    return $this->hasMany(InvoiceTax::class);
  }

  public function updateTaxAmount(): void
  {
    $fixed_tax = $this->taxes()->where('invoice_taxes.type', 'Fixed')->sum('invoice_taxes.amount') / 1000;
    $percent_tax = $this->taxes()->where('invoice_taxes.type', 'Percent')->sum('invoice_taxes.amount') / 1000;

    $this->update(['total_tax_amount' => $fixed_tax + (($this->invoiceable->estimated_cost ?? $this->invoiceable->total) * $percent_tax / 100)]);
  }
}
