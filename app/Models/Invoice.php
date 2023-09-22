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
    'terms',
    'status'
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
    return $this->hasMany(InvoiceItem::class);
  }

  public function milestones()
  {
    return $this->morphedByMany(ContractMilestone::class, 'invoiceable', 'invoice_items', 'invoice_id', 'invoiceable_id')->withPivot('amount', 'description');
    // return $this->belongsToMany(ContractMilestone::class, 'invoice_items', 'invoice_id', 'invoiceable_id')->where('invoiceable_type', ContractMilestone::class);
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

  public function updateItemsTaxType(): void
  {
    $this->items->each(function ($item) {
      $item->updateTaxAmount();
    });
  }

  public function updateSubtotal(): void
  {
    $subtotal = $this->items()->sum('amount');
    $this->update(['subtotal' => $subtotal, 'total' => $subtotal + $this->total_tax]);
  }

  public function updateTaxAmount(): void
  {
    if($this->is_summary_tax){
      $fixed_tax = $this->taxes()->where('invoice_taxes.type', 'Fixed')->sum('invoice_taxes.amount');
      $percent_tax = $this->taxes()->where('invoice_taxes.type', 'Percent')->sum('invoice_taxes.amount');
      $this->update(['total_tax' => $fixed_tax + ($this->subtotal * $percent_tax / 100)]);
    }else{
      $fixed_tax = $this->items()->sum('invoice_items.total_tax_amount');
      $this->update(['total_tax' => $fixed_tax]);
    }

    $this->updateSubtotal();
  }
}
