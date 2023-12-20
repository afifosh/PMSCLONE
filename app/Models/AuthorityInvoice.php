<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Comments\Models\Concerns\HasComments;

class AuthorityInvoice extends Model
{
  use HasFactory, SoftDeletes, HasComments;

  protected $fillable = [
    'invoice_id',
    'total_wht',
    'total_rc',
    'total',
    'rounding_amount',
    'paid_wht_amount',
    'paid_rc_amount',
    'due_date',
    'status',
  ];

  protected $casts = [
    'due_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  const STATUSES = [
    'Draft',
    'Sent',
    'Paid',
    'Partial Paid',
    'Void'
  ];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function getTotalAttribute($value)
  {
    return ($value / 1000) + $this->rounding_amount;
  }

  public function setTotalAttribute($value)
  {
    $this->attributes['total'] = moneyToInt($value);
  }

  public function getRoundingAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setRoundingAmountAttribute($value)
  {
    $this->attributes['rounding_amount'] = moneyToInt($value);
  }

  public function getTotalWhtAttribute($value)
  {
    return $value / 1000;
  }

  public function setTotalWhtAttribute($value)
  {
    $this->attributes['total_wht'] = moneyToInt($value);
  }

  public function getTotalRcAttribute($value)
  {
    return $value / 1000;
  }

  public function setTotalRcAttribute($value)
  {
    $this->attributes['total_rc'] = moneyToInt($value);
  }

  public function getPaidAmountAttribute()
  {
    return $this->paid_wht_amount + $this->paid_rc_amount;
  }

  public function getPaidWhtAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setPaidWhtAmountAttribute($value)
  {
    $this->attributes['paid_wht_amount'] = moneyToInt($value);
  }

  public function getPaidRcAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setPaidRcAmountAttribute($value)
  {
    $this->attributes['paid_rc_amount'] = moneyToInt($value);
  }

  public function getRemainingRcAttribute()
  {
    return $this->total_rc - $this->paid_rc_amount;
  }

  public function getRemainingWhtAttribute()
  {
    return $this->total_wht - $this->paid_wht_amount;
  }

  /**
   * Get the amount which can be paid against this invoice.
   */
  public function payableAmount()
  {
    return $this->total - $this->paid_rc_amount - $this->paid_wht_amount;
  }

  /**
   * Get the WHT which can be paid against this invoice.
   */
  public function payableWHT()
  {
    return ($this->total_wht - $this->paid_wht_amount);
  }

  /**
   * Get the RC which can be paid against this invoice.
   */
  public function payableRC()
  {
    return ($this->total_rc - $this->paid_rc_amount);
  }

  public function getFormatedId()
  {
    return runtimeTAInvIdFormat($this->id);
  }

  public function scopeApplyRequestFilters($q)
  {
    $q->when(request()->filter_company, function ($q) {
      $q->whereHas('invoice', function ($q) {
        $q->where('company_id', request()->filter_company);
      });
    })->when(request()->filter_contract, function ($q) {
      $q->whereHas('invoice', function ($q) {
        $q->where('contract_id', request()->filter_contract);
      });
    })->when(request()->filter_status, function ($q) {
      $q->where('status', request()->filter_status);
    })->when(request()->filter_due_date == 'this_month', function ($q) {
      $q->whereBetween('due_date', [today()->startOfMonth(), today()->endOfMonth()]);
    })->when(request()->filter_due_date == 'next_month', function ($q) {
      $q->whereBetween('due_date', [today()->addMonth()->startOfMonth(), today()->addMonth()->endOfMonth()]);
    })->when(request()->filter_due_date == 'prev_month', function ($q) {
      $q->whereBetween('due_date', [today()->subMonth()->startOfMonth(), today()->subMonth()->endOfMonth()]);
    })->when(request()->filter_due_date == 'over_due', function ($q) {
      $q->where('due_date', '<', today())->where('status', '!=', 'Paid');
    })->when(request()->filter_due_date == 'this_quarter', function ($q) {
      $q->whereBetween('due_date', [today()->startOfQuarter(), today()->endOfQuarter()]);
    })->when(request()->filter_due_date == 'prev_quarter', function ($q) {
      $q->whereBetween('due_date', [today()->subQuarter()->startOfQuarter(), today()->subQuarter()->endOfQuarter()]);
    })->when(request()->filter_due_date == 'next_quarter', function ($q) {
      $q->whereBetween('due_date', [today()->addQuarter()->startOfQuarter(), today()->addQuarter()->endOfQuarter()]);
    })->when(request()->notvoid, function ($q) {
      $q->where('status', '!=', 'Void');
    });
  }

  public function payments()
  {
    return $this->morphMany(InvoicePayment::class, 'payable');
  }

  /*
  * This string will be used in notifications on what a new comment
  * was made.
  */
  public function commentableName(): string
  {
    return 'Invoice: TA-' . runtimeInvIdFormat($this->id) . ' Of contract: ' . $this->invoice->contract->subject;
  }

  /*
  * This URL will be used in notifications to let the user know
  * where the comment itself can be read.
  */
  public function commentUrl(): string
  {
    return route('admin.contracts.invoices.edit', [$this->invoice->contract_id, $this->invoice_id, 'tab' => 'authority-tax', 'popup' => 'comments']);
  }

  /**
   * Invoices which have active ACL rule for the given admin
   */
  public function scopeValidAccessibleByAdmin($q, $admin_id)
  {
    $q->whereHas('invoice', function ($q) use ($admin_id) {
      $q->validAccessibleByAdmin($admin_id);
    });
  }

  /**
   * Scope a query to only include invoices which are payable
   */
  public function scopePayable($q)
  {
    // invoice must have some amount to be paid and parent invoice must be paid
    $q->where('total', '>', 0)
      ->whereRaw('total - paid_rc_amount - paid_wht_amount > 0')
      ->whereHas('invoice', function ($q) {
        $q->where('status', 'Paid');
      });
  }
}
