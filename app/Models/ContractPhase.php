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
    'order'
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
    if(!$this->deduction)
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
    return $q->when(request()->filter_status, function ($q) {
      if (request()->filter_status == 'Draft') return $q->where('contracts.status', 'Draft');
      else if (request()->filter_status == 'Not started') {
        $q->where('start_date', '>', now());
      } elseif (request()->filter_status == 'Expired') {
        $q->where('contracts.status', 'Active')->where('end_date', '<', now());
      } elseif (request()->filter_status == 'Terminated') {
        $q->where('contracts.status', 'Terminated');
      } elseif (request()->filter_status == 'Paused') {
        $q->where('contracts.status', 'Paused');
      } elseif (request()->filter_status == 'Active') {
        $q->where('contracts.status', 'Active')->where('contracts.start_date', '<=', now())->where('contracts.end_date', '>=', now()); //->where('end_date', '>=', now()->addWeeks(2));
      } elseif (request()->filter_status == 'About To Expire') {
        $q->where('contracts.status', 'Active')->where('contracts.end_date', '>', now())->where('contracts.end_date', '<', now()->addMonth());
      }
    })->when(request()->companies, function ($q) {
      $q->where('contracts.assignable_type', Company::class)->where('contracts.assignable_id', request()->companies);
    })->when(request()->search_q, function ($q) {
      $q->where(function ($q) {
        $q->where('contracts.subject', 'like', '%' . request()->search_q . '%')
          ->orWhereHas('phases', function ($q) {
            $q->where('name', 'like', '%' . request()->search_q . '%');
          });
      });
    })->when(request()->contract_type, function ($q) {
      $q->where('contracts.type_id', request()->contract_type);
    })->when(request()->contracts, function ($q) {
      $q->where('contracts.id', request()->contracts);
    })->when(request()->contract_category, function ($q) {
      $q->where('contracts.category_id', request()->contract_category);
    })->when(request()->projects, function ($q) {
      $q->whereHas('project', function ($q) {
        $q->where('id', request()->projects);
      });
    })->when(request()->programs, function ($q) {
      $programs = request()->programs;

      // Ensure that $programs is an array of integers
      if (!is_array($programs)) {
        $programs = [$programs]; // Wrap the integer in an array
      }

      // Cast each element in the $programs array to an integer
      $programs = array_map('intval', $programs);
      // Fetch IDs for all children of the given program
      $childProgramIds = Program::where('parent_id', request()->programs)->pluck('id')->toArray();

      // Include the main program's ID
      //    $programIds = array_merge(request()->programs, $childProgramIds);
      //   $programs = request()->programs;

      // // Cast each element in the $programs array to an integer
      // $programs = array_map('intval', $programs);
      // Use these IDs to filter contracts
      $q->whereIn('contracts.program_id',   $programs);
    })->when(request()->date_range && @explode(' to ', request()->date_range)[0], function ($q) {
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
    })->join('contract_stages as cs1', 'contract_phases.stage_id', '=', 'cs1.id')
      ->join('contracts as c1', 'cs1.contract_id', '=', 'c1.id')
      ->leftJoin('programs as p1', 'c1.program_id', '=', 'p1.id')
    ->when(request()->has('dnh-regular-invoice') && request()->get('dnh-regular-invoice'), function ($q) {
      $q->whereDoesntHave('addedAsInvoiceItem.invoice', function ($q) {
        $q->where('type', 'Regular');
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
    if(!$this->deduction) return;
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

  /**
   * Sync Taxes, deduction, total with invoices
   */
  public function syncUpdateWithInvoices(string $except = null): void
  {
    $this->load('addedAsInvoiceItem.invoice', 'pivotTaxes', 'deduction');

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
          $this->pivotTaxes()->whereIn('category', [2,3])->sum(DB::raw('COALESCE(NULLIF(manual_amount, 0), calculated_amount)')) / 1000,
          'total' => $this->getRawOriginal('total_cost') / 1000,
          'subtotal_amount_adjustment' => $this->subtotal_amount_adjustment,
          'total_amount_adjustment' => $this->total_amount_adjustment,
          'rounding_amount' => $this->rounding_amount,
        ]);

        $item->taxes()->detach();

        foreach($this->pivotTaxes as $tax){
          $item->taxes()->attach($tax->tax_id, [
            'amount' => $tax->getRawOriginal('amount'),
            'type' => $tax->type,
            'invoice_id' => $item->invoice_id,
            'calculated_amount' => $tax->getRawOriginal('calculated_amount'),
            'manual_amount' => $tax->getRawOriginal('manual_amount'),
            'category' => $tax->category,
          ]);
        }

        if($this->deduction){
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
        }else{
          $item->deduction()->delete();
        }

        $item->invoice->reCalculateTotal();
      });
    }
  }
}
