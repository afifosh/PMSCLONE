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
    'is_summary_tax',
    'subtotal',
    'total_tax',
    'total',
    'rounding_amount',
    'paid_amount',
    'downpayment_amount',
    'discount_type',
    'discount_percentage',
    'discount_amount',
    'adjustment_description',
    'adjustment_amount',
    'retention_id',
    'retention_name',
    'retention_percentage',
    'retention_amount',
    'retention_released_at',
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

  public function setSubtotalAttribute($value)
  {
    $this->attributes['subtotal'] = moneyToInt($value);
  }

  public function getTotalAttribute($value)
  {
    return ($value / 1000) + $this->rounding_amount;
  }

  public function setTotalAttribute($value)
  {
    $this->attributes['total'] = moneyToInt($value);
  }

  public function getPaidAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setPaidAmountAttribute($value)
  {
    $this->attributes['paid_amount'] = moneyToInt($value);
  }

  public function getTotalTaxAttribute($value)
  {
    return $value / 1000;
  }

  public function setTotalTaxAttribute($value)
  {
    $this->attributes['total_tax'] = moneyToInt($value);
  }

  public function getDiscountAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setDiscountAmountAttribute($value)
  {
    $this->attributes['discount_amount'] = moneyToInt($value);
  }

  public function getAdjustmentAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAdjustmentAmountAttribute($value)
  {
    $this->attributes['adjustment_amount'] = moneyToInt($value);
  }

  public function getRetentionAmountAttribute($value)
  {
    if ($this->retention_released_at) {
      return 0;
    }

    return $value / 1000;
  }

  public function setRetentionAmountAttribute($value)
  {
    $this->attributes['retention_amount'] = moneyToInt($value);
  }

  public function getDownpaymentAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setDownpaymentAmountAttribute($value)
  {
    $this->attributes['downpayment_amount'] = moneyToInt($value);
  }

  public function getRoundingAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setRoundingAmountAttribute($value)
  {
    $this->attributes['rounding_amount'] = moneyToInt($value);
  }

  /**
   * Get the amount which can be paid against this invoice.
   */
  public function payableAmount()
  {
    return $this->total - $this->paid_amount - $this->retention_amount - $this->downpayment_amount;
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
}
