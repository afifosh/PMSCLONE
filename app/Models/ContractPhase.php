<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    'manual_tax_amount',
    'total_cost',
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

  public function getManualTaxAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setManualTaxAmountAttribute($value)
  {
    $this->attributes['manual_tax_amount'] = moneyToInt($value);
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
    return $value / 1000;
  }

  public function setTotalCostAttribute($value)
  {
    $this->attributes['total_cost'] = moneyToInt($value);
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
      ->leftJoin('programs as p1', 'c1.program_id', '=', 'p1.id');
  }


  public function contract(): BelongsTo
  {
    return $this->belongsTo(Contract::class);
  }

  public function reviews()
  {
    return $this->morphMany(Review::class, 'reviewable');
  }

  public function stage(): BelongsTo
  {
    return $this->belongsTo(ContractStage::class);
  }

  public function addedAsInvoiceItem()
  {
    // have invoices table and invoice_items table. Invoice items table is polymorphic many to many. so checking is this phase class is added as invoice item
    return $this->morphMany(InvoiceItem::class, 'invoiceable');
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
    $total = $this->invoices()->sum('total') / 1000;
    return $this->total_cost - $total;
  }

  public function taxes(): BelongsToMany
  {
    return $this->belongsToMany(Tax::class, 'phase_taxes')->withPivot('amount', 'type');
  }

  public function updateTaxAmount(): void
  {
    $fixed_tax = $this->taxes()->where('phase_taxes.type', 'Fixed')->sum('phase_taxes.amount');
    $percent_tax = $this->taxes()->where('phase_taxes.type', 'Percent')->sum('phase_taxes.amount');
    $tax_amount = $this->estimated_cost * ($percent_tax / (100 * 1000)) + $fixed_tax;
    $total_cost = $this->estimated_cost + $tax_amount + $this->adjustment_amount; // Added the adjustment here

    $this->update(['tax_amount' => $tax_amount, 'total_cost' => $total_cost]);
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
}
