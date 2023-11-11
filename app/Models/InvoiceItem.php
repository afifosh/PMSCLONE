<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class InvoiceItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'invoiceable_type',
    'invoiceable_id',
    'subtotal',
    'total_tax_amount',
    'manual_tax_amount',
    'description',
    'order',
    'total',
    'rounding_amount',
  ];

  protected $casts = [
    // 'amount' => 'integer',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getSubtotalAttribute($value)
  {
    return $value / 1000;
  }

  public function setSubtotalAttribute($value)
  {
    $this->attributes['subtotal'] = moneyToInt($value);
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

  public function getRoundingAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setRoundingAmountAttribute($value)
  {
    $this->attributes['rounding_amount'] = moneyToInt($value);
  }

  public function getTotalAttribute($value)
  {
    return ($value / 1000) + $this->rounding_amount ;
  }

  public function setTotalAttribute($value)
  {
    $this->attributes['total'] = moneyToInt($value);
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
    return $this->belongsToMany(InvoiceConfig::class, 'invoice_taxes', 'invoice_item_id', 'tax_id')->withPivot('amount', 'type');
  }

  /**
   * pivot table for storing taxes so that updating main tax table doesn't affect old invoices.
   * Tax amount for invoices is calculated from this table.
   */
  public function pivotTaxes()
  {
    return $this->hasMany(InvoiceTax::class);
  }

  /**
   * Sync taxes for invoice item
   * @param Collection $taxes
   */
  public function syncTaxes(Collection|Array $taxes): void
  {
    $sync_data = [];
    foreach ($taxes as $rate) {
      $sync_data[$rate->id] = ['amount' => $rate->getRawOriginal('amount'), 'type' => $rate->type, 'invoice_item_id' => $this->id, 'invoice_id' => $this->invoice_id];
    }

    $this->taxes()->sync($sync_data);
  }

  /**
   * pivot table for storing deduction information
   *
   * @return MorphOne
   */
  public function deduction(): MorphOne
  {
    return $this->morphOne(InvoiceDeduction::class, 'deductible');
  }
}
