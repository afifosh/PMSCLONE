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
    $this->attributes['amount'] = round($value * 1000);
  }

  public function getTotalTaxAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setTotalTaxAmountAttribute($value)
  {
    $this->attributes['total_tax_amount'] = round($value * 1000);
  }

  // public function invoice()
  // {
  //   return $this->belongsTo(Invoice::class);
  // }

  public function invoiceable()
  {
    return $this->morphTo();
  }

  public function taxes(): BelongsToMany
  {
    return $this->belongsToMany(Tax::class, 'invoice_taxes')->withPivot('amount', 'type');
  }

  public function updateTaxAmount(): void
  {
    $fixed_tax = $this->taxes()->where('invoice_taxes.type', 'Fixed')->sum('invoice_taxes.amount');
    $percent_tax = $this->taxes()->where('invoice_taxes.type', 'Percent')->sum('invoice_taxes.amount');
    $this->update(['total_tax_amount' => $fixed_tax + ($this->invoiceable->estimated_cost ?? $this->invoiceable->total * $percent_tax / 100)]);
  }
}
