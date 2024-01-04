<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;
use Spatie\Comments\Models\Concerns\HasComments;

class ContractPhase extends BaseModel
{
  use HasFactory, HasComments;

  protected $fillable = [
    'contract_id',
    'stage_id',
    'name',
    'estimated_cost',
    'tax_amount',
    'total_cost',
    'subtotal_amount_adjustment',
    'total_amount_adjustment',
    'rounding_amount',
    'start_date',
    'due_date',
    'description',
    'order',
    'is_allowable_cost'
  ];

  protected $appends = ['status'];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public const STATUSES = [
    'Not started',
    'Active',
    'About To Expire',
    'Expired',
  ];

  public const STATUSCOLORS = [
    'Not started' => 'warning',
    'Active' => 'success',
    'About To Expire' => 'warning',
    'Expired' => 'danger',
  ];

  public function getEstimatedCostAttribute($value)
  {
    return $value / 1000;
  }

  public function setEstimatedCostAttribute($value)
  {
    $this->attributes['estimated_cost'] = moneyToInt($value);
  }

  public function getTaxAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setTaxAmountAttribute($value)
  {
    $this->attributes['tax_amount'] = moneyToInt($value);
  }

  public function getTotalCostAttribute($value)
  {
    return ($value) / 1000 + $this->total_amount_adjustment;
  }

  public function setTotalCostAttribute($value)
  {
    $this->attributes['total_cost'] = moneyToInt($value);
  }

  public function getRoundingAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setRoundingAmountAttribute($value)
  {
    $this->attributes['rounding_amount'] = moneyToInt($value);
  }

  public function getSubtotalAmountAdjustmentAttribute($value)
  {
    return $value / 1000;
  }

  public function setSubtotalAmountAdjustmentAttribute($value)
  {
    $this->attributes['subtotal_amount_adjustment'] = moneyToInt($value);
  }

  public function getTotalAmountAdjustmentAttribute($value)
  {
    return $value / 1000;
  }

  public function setTotalAmountAdjustmentAttribute($value)
  {
    $this->attributes['total_amount_adjustment'] = moneyToInt($value);
  }

  /**
   * subtotal row in phase edit table
   */
  public function getSubtotalRowRawAttribute()
  {
    if (!$this->deduction)
      return $this->estimated_cost;
    if ($this->deduction->is_before_tax) {
      return $this->estimated_cost - $this->deduction->amount;
    } else {
      return $this->estimated_cost + $this->tax_amount;
    }
  }

  /**
   * subtotal row in phase edit table
   */
  public function getSubtotalRowAttribute()
  {
    return $this->subtotal_row_raw + $this->subtotal_amount_adjustment;
  }

  public function getStatusAttribute()
  {
    // if($this->due_date->isPast()) return 'Expired';
    // elseif($this->start_date->isFuture()) return 'Not started';
    // elseif(now() > $this->due_date->subMonth()) return 'About To Expire';
    // elseif(now() >= $this->start_date) return 'Active';
    $value = $this->getRawOriginal('status');
    if ($value == 'Terminated' || $value == 'Paused' || $value == 'Draft') return $value;

    if ($this->due_date === null && $this->start_date === null)
      return '';

    if ($this->due_date == null && $this->start_date) {
      if ($this->start_date->isSameDay(today())) {
        return "Active";
      } elseif ($this->start_date->isFuture()) {
        return "Not Started";
      } else {
        return "Expired";
      }
    } else {
      if ($this->due_date->isPast()) {
        return "Expired";
      } elseif ($this->start_date->isFuture()) {
        return "Not Started";
      } elseif (now()->diffInDays($this->due_date) <= 30) {
        return "About To Expire";
      } else {
        return "Active";
      }
    }
  }

  public function scopeApplyRequestFilters($q)
  {
    return $q->when(request()->date_range && @explode(' to ', request()->date_range)[0], function ($q) {
      try {
        $date = Carbon::parse(explode(' to ', request()->date_range)[0]);
        $q->where('start_date', '>=', $date);
      } catch (\Exception $e) {
      }
    })->when(request()->date_range && @explode(' to ', request()->date_range)[1], function ($q) {
      try {
        $date = Carbon::parse(explode(' to ', request()->date_range)[1]);
        $q->where('end_date', '<=', $date);
      } catch (\Exception $e) {
      }
    })
      ->when(request()->has('dnh-regular-invoice') && request()->get('dnh-regular-invoice'), function ($q) {
        $q->whereDoesntHave('addedAsInvoiceItem.invoice', function ($q) {
          $q->where('type', 'Regular');
        });
      })
      ->when(request()->dependent_2_col == 'creating-inv-type' && request()->get('dependent_2'), function ($q) {
        // invoice creating dependent filter
        $q->when(request()->get('dependent_2') == 'Partial Invoice', function ($q) {
          // user is creating partial invoice, query the contracts which has allowable phases.
          $q->where('is_allowable_cost', 1);
        })
          ->when(request()->get('dependent_2') == 'Regular', function ($q) {
            // user is creating regular invoice, query the contractw which has regular phases.
            $q->where('is_allowable_cost', 0);
          });
      })->when(request()->phase_reviewer && request()->phase_review_status, function ($q) {
        $q->when(request()->phase_review_status == 'reviewed', function ($q) {
          $q->whereHas('reviews', function ($q) {
            $q->where('user_id', request()->phase_reviewer);
          });
        })->when(request()->phase_review_status == 'not_reviewed', function ($q) {
          $q->whereDoesntHave('reviews', function ($q) {
            $q->where('user_id', request()->phase_reviewer);
          });
        });
      });
  }


  public function contract(): BelongsTo
  {
    return $this->belongsTo(Contract::class);
  }

  public function reviews()
  {
    return $this->morphMany(Review::class, 'reviewable');
  }

  /**
   * Admins who reviewed this phase
   */
  public function reviewdByAdmins()
  {
    return $this->belongsToMany(Admin::class, 'reviews', 'reviewable_id', 'user_id')
      ->where('reviewable_type', self::class);
  }

  public function stage(): BelongsTo
  {
    return $this->belongsTo(ContractStage::class);
  }

  public function addedAsInvoiceItem()
  {
    // have invoices table and invoice_items table. Invoice items table is polymorphic many to many. so checking is this phase class is added as invoice item
    return $this->morphMany(InvoiceItem::class, 'invoiceable')->has('invoice');
  }

  public function addedInPaidInvoices()
  {
    return $this->morphMany(InvoiceItem::class, 'invoiceable')->whereHas('invoice', function ($q) {
      $q->whereIn('status', ['Paid', 'Partial Paid', 'Retention Withheld']);
    });
  }

  /**
   * Invoice Items which are being paid against this phase. with invoice
   */
  public function addedInPaidInvoicesWithInvoice()
  {
    return $this->morphMany(InvoiceItem::class, 'invoiceable')->whereHas('invoice', function ($q) {
      $q->whereIn('status', ['Paid', 'Partial Paid', 'Retention Withheld']);
    })->with('invoice');
  }

  /**
   * Amount paid against allowable cost including deduction.
   */
  public function paidAmountAgainstAllowableCost()
  {
    return $this->addedInPaidInvoicesWithInvoice->sum(function ($item) {
      return $item->invoice->paid_amount;
      // deduction not included from the custom items, because deduction is added in the main phase and deducted from total cost.
      //+ (@$item->deduction->manual_amount ? $item->deduction->manual_amount : (@$item->deduction->amount ? $item->deduction->amount : 0));
    });
  }

  /**
   * is this phase partially paid
   */
  public function isPartialPaid()
  {
    $paid_amount = $this->paidAmountAgainstAllowableCost();

    return $paid_amount && $paid_amount < $this->total_cost;
  }

  /**
   * Is this phase fully paid
   */
  public function isPaid(): bool
  {
    return $this->addedInPaidInvoices->count() > 0;

    // if (!$this->is_allowable_cost) {
    //   // if not allowable cost, means paid in signle invoice.
    //   return $this->addedInPaidInvoices->count() > 0;
    // } else {
    //   // might be paid in partial invoices.
    //   return $this->paidAmountAgainstAllowableCost() >= $this->total_cost;
    // }
  }

  public function invoices(): BelongsToMany
  {
    return $this->belongsToMany(Invoice::class, 'invoice_items', 'invoiceable_id', 'invoice_id')->where('invoiceable_type', ContractPhase::class);
  }

  /**
   * The remaining amount to be invoiced
   */
  public function getRemainingAmount()
  {
    $total = $this->invoices()->sum('invoices.total') / 1000;
    return $this->total_cost - $total;
  }

  public function taxes(): BelongsToMany
  {
    return $this->belongsToMany(InvoiceConfig::class, 'phase_taxes', 'contract_phase_id', 'tax_id')->withPivot('id', 'amount', 'type', 'calculated_amount', 'manual_amount', 'category')->withTimestamps();
  }

  public function pivotTaxes(): HasMany
  {
    return $this->hasMany(PhaseTax::class, 'contract_phase_id');
  }

  /*
 * This string will be used in notifications on what a new comment
 * was made.
 */
  public function commentableName(): string
  {
    return 'Phase: ' . $this->name . ' Of contract: ' . $this->contract->subject;
  }

  /*
* This URL will be used in notifications to let the user know
* where the comment itself can be read.
*/
  public function commentUrl(): string
  {
    return '#';
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

  public function recalculateDeductionAmount($reset_manual_amount = true): void
  {
    $this->load('deduction');
    if (!$this->deduction) return;
    $phaseTaxes = PhaseTax::where('contract_phase_id', $this->id)
      ->select([
        'category',
        DB::raw('COALESCE(NULLIF(manual_amount, 0), calculated_amount) as total_amount')
      ])
      ->get();

    $simpleTax = $phaseTaxes->where('category', 1)->sum('total_amount') / 1000;
    $reverseCharge = $phaseTaxes->where('category', 2)->sum('total_amount') / 1000;
    $total_tax = $simpleTax - $reverseCharge;
    $deductionAmount = $this->calculateDeductionAmount($total_tax);
    $deduction = $this->deduction;
    if ($deduction && $deduction->manual_amount && $reset_manual_amount) {
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
        $deductionAmount = ($this->estimated_cost * $this->deduction->percentage) / 100;
      } else {
        $deductionAmount = ($this->estimated_cost + $total_tax) * $this->deduction->percentage / 100;
      }
    }

    return $deductionAmount;
  }

  public function reCalculateTotal(): void
  {
    $this->load('deduction');

    $phaseTaxes = PhaseTax::where('contract_phase_id', $this->id)
      ->select([
        'category',
        DB::raw('COALESCE(NULLIF(manual_amount, 0), calculated_amount) as total_amount')
      ])
      ->get();

    $simpleTax = $phaseTaxes->where('category', 1)->sum('total_amount') / 1000;
    $behalfTax = $phaseTaxes->where('category', 2)->sum('total_amount') / 1000;
    $this->tax_amount = $simpleTax + $behalfTax;
    $this->total_cost = $this->estimated_cost + $simpleTax - $behalfTax - ($this->deduction ? ($this->deduction->manual_amount ? $this->deduction->manual_amount : $this->deduction->amount) : 0);
    $this->subtotal_amount_adjustment = 0;
    $this->total_amount_adjustment = 0;
    $this->save();
  }

  public function reCalculateCost()
  {
    $this->load('costAdjustments');

    $this->total_cost = $this->estimated_cost + $this->costAdjustments->sum('amount');

    $this->save();
  }

  /**
   * Recalculate tax amounts and reset manual amounts to 0
   * This function is called when deduction is created
   */
  public function reCalculateTaxAmountsAndResetManualAmounts($considerDeduction = true): void
  {
    $this->load('pivotTaxes');
    $taxableAmount = $this->estimated_cost - (($considerDeduction && $this->deduction && $this->deduction->is_before_tax) ? ($this->deduction->manual_amount ? $this->deduction->manual_amount : $this->deduction->amount) : 0);
    foreach ($this->pivotTaxes as $tax) {
      if ($tax->type == 'Fixed') {
        continue;
      } else {
        $tax->manual_amount = 0;
        $tax->calculated_amount = $taxableAmount * $tax->amount / 100;
        $tax->save();
      }
    }
  }

  /**
   * Sync Taxes, deduction, total with invoices
   */
  public function syncUpdateWithInvoices(string $except = null): void
  {
    $this->load('addedAsInvoiceItem.invoice', 'pivotTaxes', 'deduction', 'costAdjustments');

    // if added in invoice then update invoice item and tax amount, deduction amount and total
    if ($this->addedAsInvoiceItem->count()) {
      $this->addedAsInvoiceItem->each(function ($item) use ($except) {
        // if $except is passed then skip this item
        if ($except && $item->invoice_id == $except) return;

        $item->update([
          'subtotal' => $this->estimated_cost,
          'total_tax_amount' => $this->tax_amount,
          'authority_inv_total' =>
          // pivot taxes
          $this->pivotTaxes()->whereIn('category', [2, 3])->sum(DB::raw('COALESCE(NULLIF(manual_amount, 0), calculated_amount)')) / 1000,
          'total' => $this->getRawOriginal('total_cost') / 1000,
          'subtotal_amount_adjustment' => $this->subtotal_amount_adjustment,
          'total_amount_adjustment' => $this->total_amount_adjustment,
          'rounding_amount' => $this->rounding_amount,
        ]);

        $item->taxes()->detach();

        foreach ($this->pivotTaxes as $tax) {
          $item->taxes()->attach($tax->tax_id, [
            'amount' => $tax->getRawOriginal('amount'),
            'type' => $tax->type,
            'invoice_id' => $item->invoice_id,
            'calculated_amount' => $tax->getRawOriginal('calculated_amount'),
            'manual_amount' => $tax->getRawOriginal('manual_amount'),
            'category' => $tax->category,
          ]);
        }

        if ($this->deduction) {
          $item->deduction()->updateOrCreate([
            'deductible_id' => $item->id,
            'deductible_type' => InvoiceItem::class,
          ], [
            'downpayment_id' => $this->deduction->downpayment_id,
            'dp_rate_id' => $this->deduction->dp_rate_id,
            'is_percentage' => $this->deduction->is_percentage,
            'amount' => $this->deduction->amount,
            'manual_amount' => $this->deduction->manual_amount,
            'percentage' => $this->deduction->percentage,
            'is_before_tax' => $this->deduction->is_before_tax,
            'calculation_source' => $this->deduction->calculation_source,
          ]);
        } else {
          $item->deduction()->delete();
        }

        // if ($this->costAdjustments->count()) {
        //   $item->costAdjustments()->delete();
        //   $this->costAdjustments->each(function ($costAdjustment) use ($item) {
        //     $item->costAdjustments()->create([
        //       'amount' => $costAdjustment->amount,
        //       'description' => $costAdjustment->description,
        //     ]);
        //   });
        // }

        $item->invoice->reCalculateTotal();
      });
    }
  }

  /**
   * Phase Has Many cost adjustments
   */
  public function costAdjustments(): HasMany
  {
    return $this->hasMany(PhaseCostAdjustment::class, 'phase_id');
  }
}
