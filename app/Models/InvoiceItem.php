<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

class InvoiceItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'invoiceable_type',
    'invoiceable_id',
    'description',
    'subtotal',
    'total_tax_amount',
    'description',
    'order',
    'total',
    'authority_inv_total',
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
    return ($value / 1000) + $this->rounding_amount;
  }

  public function setTotalAttribute($value)
  {
    $this->attributes['total'] = moneyToInt($value);
  }

  public function getAuthorityInvTotalAttribute($value)
  {
    return $value / 1000;
  }

  public function setAuthorityInvTotalAttribute($value)
  {
    $this->attributes['authority_inv_total'] = moneyToInt($value);
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
    return $this->belongsToMany(InvoiceConfig::class, 'invoice_taxes', 'invoice_item_id', 'tax_id')->withPivot('amount', 'type', 'calculated_amount', 'manual_amount', 'is_simple_tax', 'pay_on_behalf', 'is_authority_tax', 'id');
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
   * pivot table for storing deduction information
   *
   * @return MorphOne
   */
  public function deduction(): MorphOne
  {
    return $this->morphOne(InvoiceDeduction::class, 'deductible');
  }

  public function recalculateDeductionAmount(): void
  {
    $this->load('deduction');
    $invoiceTaxes = InvoiceTax::where('invoice_item_id', $this->id)
      ->select([
        'pay_on_behalf',
        'is_authority_tax',
        'is_simple_tax',
        DB::raw('COALESCE(NULLIF(manual_amount, 0), calculated_amount) as total_amount')
      ])
      ->get();

    $simpleTax = $invoiceTaxes->where('pay_on_behalf', false)->sum('total_amount') / 1000;
    $behalfTax = $invoiceTaxes->where('pay_on_behalf', true)->sum('total_amount') / 1000;
    $total_tax = $simpleTax - $behalfTax;
    $deductionAmount = $this->calculateDeductionAmount($total_tax);
    // if manua amount difference is greater than 1 then reset manual amount
    $deduction = $this->deduction;
    if ($deduction && $deduction->manual_amount && abs($deduction->manual_amount - $deductionAmount) > 1) {
      $deduction->manual_amount = 0;
    }
    $deduction->amount = $deductionAmount;
    $deduction->save();
  }

  private function calculateDeductionAmount($total_tax = 0)
  {
    $deductionAmount = 0;
    if (!$this->deduction) {
      return $deductionAmount;
    }

    if (!$this->deduction->is_percentage) {
      $deductionAmount = $this->deduction->manual_amount ? $this->deduction->manual_amount : $this->deduction->amount;
      return $deductionAmount;
    } else {
      if ($this->deduction->source == 'Down Payment')
        $deductionAmount = $this->deduction->downpayment->total * $this->deduction->percentage / 100;
      elseif ($this->deduction->is_before_tax) {
        $deductionAmount = ($this->subtotal * $this->deduction->percentage) / 100;
      } else {
        $deductionAmount = ($this->subtotal + $total_tax) * $this->deduction->percentage / 100;
      }
    }

    return $deductionAmount;
  }

  public function reCalculateTotal($deductionUpdated = false, $taxUpdated = false): void
  {
    $this->load('deduction');

    $invoiceTaxes = InvoiceTax::where('invoice_item_id', $this->id)
      ->select([
        'pay_on_behalf',
        'is_authority_tax',
        'is_simple_tax',
        DB::raw('COALESCE(NULLIF(manual_amount, 0), calculated_amount) as total_amount')
      ])
      ->get();

    $simpleTax = $invoiceTaxes->where('pay_on_behalf', false)->sum('total_amount') / 1000;
    $behalfTax = $invoiceTaxes->where('pay_on_behalf', true)->sum('total_amount') / 1000;
    $authorityTax = $invoiceTaxes->where('is_authority_tax', true)->sum('total_amount') / 1000;
    $this->total_tax_amount = $simpleTax + $behalfTax;
    $this->total = $this->subtotal + $simpleTax - $behalfTax - ($this->deduction ? ($this->deduction->manual_amount ? $this->deduction->manual_amount : $this->deduction->amount) : 0);
    $this->authority_inv_total = $authorityTax;
    $this->save();
  }

  /**
   * Recalculate tax amounts and reset manual amounts to 0
   * This function is called when deduction is created
   */
  public function reCalculateTaxAmountsAndResetManualAmounts($considerDeduction = true): void
  {
    $this->load('pivotTaxes');
    $taxableAmount = $this->subtotal - ($considerDeduction && $this->deduction && $this->deduction->is_before_tax ? ($this->deduction->manual_amount ? $this->deduction->manual_amount : $this->deduction->amount) : 0);
    foreach($this->pivotTaxes as $tax){
      if($tax->type == 'Fixed'){
        continue;
      }else{
        $tax->manual_amount = 0;
        $tax->calculated_amount = $taxableAmount * $tax->amount / 100;
        $tax->save();
      }
    }
  }
}
