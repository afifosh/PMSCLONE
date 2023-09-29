<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Invoice extends Model
{
  use HasFactory;

  protected $fillable = [
    'company_id',
    'contract_id',
    'invoice_date',
    'due_date',
    'sent_date',
    'creator_type',
    'creator_id',
    'subtotal',
    'total_tax',
    'total',
    'paid_amount',
    'is_summary_tax',
    'note',
    'description',
    'terms',
    'discount_type',
    'discount_percentage',
    'discount_amount',
    'adjustment_description',
    'adjustment_amount',
    'retention_id',
    'retention_name',
    'retention_percentage',
    'retention_amount',
    'status',
    'type',
    'refrence_id'
  ];

  protected $casts = [
    'invoice_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
    'sent_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function creator()
  {
    return $this->morphTo();
  }

  public function items()
  {
    return $this->hasMany(InvoiceItem::class)->orderBy('order');
  }

  public function phases()
  {
    return $this->morphedByMany(ContractPhase::class, 'invoiceable', 'invoice_items', 'invoice_id', 'invoiceable_id')->withPivot('amount', 'description');
    // return $this->belongsToMany(ContractPhase::class, 'invoice_items', 'invoice_id', 'invoiceable_id')->where('invoiceable_type', ContractPhase::class);
  }

  public function retentions()
  {
    return $this->morphedByMany(Invoice::class, 'invoiceable', 'invoice_items', 'invoice_id', 'invoiceable_id')->withPivot('amount', 'description');
  }

  public function payments()
  {
    return $this->hasMany(InvoicePayment::class);
  }

  public function taxes(): BelongsToMany
  {
    return $this->belongsToMany(Tax::class, 'invoice_taxes')->withPivot('amount', 'type', 'invoice_item_id');
  }

  public function getSubtotalAttribute($value)
  {
    return $value / 100;
  }

  public function setSubtotalAttribute($value)
  {
    $this->attributes['subtotal'] = round($value * 100, 0);
  }

  public function getTotalAttribute($value)
  {
    return $value / 100;
  }

  public function setTotalAttribute($value)
  {
    $this->attributes['total'] = round($value * 100, 0);
  }

  public function getPaidAmountAttribute($value)
  {
    return $value / 100;
  }

  public function setPaidAmountAttribute($value)
  {
    $this->attributes['paid_amount'] = round($value * 100, 0);
  }

  public function getTotalTaxAttribute($value)
  {
    return $value / 100;
  }

  public function setTotalTaxAttribute($value)
  {
    $this->attributes['total_tax'] = round($value * 100, 0);
  }

  public function getDiscountAmountAttribute($value)
  {
    return $value / 100;
  }

  public function setDiscountAmountAttribute($value)
  {
    $this->attributes['discount_amount'] = round($value * 100, 0);
  }

  public function getAdjustmentAmountAttribute($value)
  {
    return $value / 100;
  }

  public function setAdjustmentAmountAttribute($value)
  {
    $this->attributes['adjustment_amount'] = round($value * 100, 0);
  }

  public function getRetentionAmountAttribute($value)
  {
    return $value / 100;
  }

  public function setRetentionAmountAttribute($value)
  {
    $this->attributes['retention_amount'] = round($value * 100, 0);
  }

  public function updateItemsTaxType(): void
  {
    $this->items->each(function ($item) {
      $item->updateTaxAmount();
    });
  }

  public function updateSubtotal(): void
  {
    $subtotal = $this->items()->sum('amount') / 100;
    if(!$subtotal){
      $this->update(['discount_amount' => 0]);
    }
    // dd($subtotal, $this->discount_amount, $this->total_tax, $this->retention_amount, $this->adjustment_amount);
    $this->update([
      'subtotal' => $subtotal,
      'total' => $subtotal
        + $this->discount_amount // it is negative value
        + $this->total_tax // add total tax. It is calculated in updateTaxAmount() method
        + $this->retention_amount // retention amount is already negative. retention is basically deduction after applying discount, taxes and adjustments from the total. Which will be paid later on.
        + $this->adjustment_amount // adjustment amount can be negative or positive depending on the user input
    ]);
  }

  public function updateTaxAmount(): void
  {
    if ($this->is_summary_tax) {
      $fixed_tax = $this->taxes()->where('invoice_taxes.type', 'Fixed')->sum('invoice_taxes.amount');
      $percent_tax = $this->taxes()->where('invoice_taxes.type', 'Percent')->sum('invoice_taxes.amount');
      $this->update(['total_tax' => $fixed_tax + (($this->subtotal + $this->discount_amount) * $percent_tax / 100)]);
    } else {
      $fixed_tax = $this->items()->sum('invoice_items.total_tax_amount');
      $this->update(['total_tax' => $fixed_tax]);
    }

    $this->updateSubtotal();
  }

  public function reCalculateTotal(): void
  {
    $this->updateTaxAmount(); // will update subtotal and total tax
  }

  public function scopeApplyRequestFilters($q)
  {
    $q->when(request()->filter_company, function ($q) {
      $q->where('company_id', request()->filter_company);
    })->when(request()->filter_contract, function ($q) {
      $q->where('contract_id', request()->filter_contract);
    });
  }

  public function addedAsInvoiceItem()
  {
    // have invoices table and invoice_items table. Invoice items table is polymorphic many to many. so checking is this phase class is added as invoice item
    return $this->morphMany(InvoiceItem::class, 'invoiceable');
  }
}
