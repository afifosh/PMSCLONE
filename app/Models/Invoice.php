<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Mediable;

class Invoice extends Model
{
  use HasFactory, Mediable, SoftDeletes;

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
    'refrence_id',
    'retention_released_at',
    'is_auto_generated',
    'downpayment_amount',
    'downpayment_before_tax'
  ];

  protected $casts = [
    'invoice_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
    'sent_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  const STATUSES = [
    'Draft',
    'Sent',
    'Paid',
    'Partial Paid',
    'Cancelled'
  ];

  const TYPES = [
    'Regular',
    'Down Payment'
  ];

  /*
  * @constant FILES_PATH The path prefix to store uploaded Docs.
  */
  const FILES_PATH = 'invoices';

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
    return $value / 1000;
  }

  public function setSubtotalAttribute($value)
  {
    $this->attributes['subtotal'] = moneyToInt($value);
  }

  public function getTotalAttribute($value)
  {
    return $value / 1000;
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

  public function getDownpaymentBeforeTaxAttribute($value)
  {
    return $value / 1000;
  }

  public function setDownpaymentBeforeTaxAttribute($value)
  {
    $this->attributes['downpayment_before_tax'] = moneyToInt($value);
  }

  public function getDownpaymentAmountRemainingAttribute()
  {
    return $this->downpaymentAmountRemaining();
  }

  public function updateItemsTaxType(): void
  {
    $this->items->each(function ($item) {
      $item->updateTaxAmount();
    });
  }

  public function updateSubtotal(): void
  {
    $subtotal = $this->items()->sum('amount') / 1000;
    if (!$subtotal) {
      $this->update(['discount_amount' => 0]);
    }

    $downpayment_before_tax = $this->downPayments()->wherePivot('is_after_tax', 0)->sum('amount') / 1000;

    $this->update([
      'subtotal' => $subtotal,
      'downpayment_amount' => $this->downPayments()->wherePivot('is_after_tax', 1)->sum('amount') / 1000,
      'downpayment_before_tax' => $downpayment_before_tax,
      'total' => $subtotal
        + $this->discount_amount // it is negative value
        - $downpayment_before_tax // downpayment before tax is negative value
        + $this->total_tax // add total tax. It is calculated in updateTaxAmount() method
        + $this->adjustment_amount // adjustment amount can be negative or positive depending on the user input
    ]);
  }

  public function updateTaxAmount(): void
  {
    $this->updateSubtotal();
    if ($this->is_summary_tax) {
      $fixed_tax = $this->taxes()->where('invoice_taxes.type', 'Fixed')->sum('invoice_taxes.amount') / 1000;
      $percent_tax = $this->taxes()->where('invoice_taxes.type', 'Percent')->sum('invoice_taxes.amount') / 1000;
      $this->update(['total_tax' => $fixed_tax + (($this->subtotal + $this->discount_amount - $this->downpayment_before_tax) * $percent_tax / 100)]);
    } else {
      $fixed_tax = $this->items()->sum('invoice_items.total_tax_amount') / 1000;
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
    })->when(request()->filter_status, function ($q) {
      $q->where('status', request()->filter_status);
    })->when(request()->filter_type, function ($q) {
      $q->where('type', request()->filter_type);
    })->when(request()->has('haspayments'), function ($q) {
      $q->has('payments');
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
    });
  }

  public function addedAsInvoiceItem()
  {
    // have invoices table and invoice_items table. Invoice items table is polymorphic many to many. so checking is this phase class is added as invoice item
    return $this->morphMany(InvoiceItem::class, 'invoiceable');
  }

  public function isEditable()
  {
    return !in_array($this->status, ['Paid', 'Partial Paid']);
  }

  public function releaseRetention(): void
  {
    try {

      if (!$this->retention_amount || $this->retention_released_at) {
        return;
      }

      DB::transaction(function () {
        $this->payments()->create([
          'transaction_id' => 'RTN-' . $this->id . '-' . round($this->retention_amount),
          'payment_date' => now(),
          'amount' => $this->retention_amount,
          'note' => 'Retention released',
          'type' => 1
        ]);

        $this->update([
          'paid_amount' => $this->paid_amount + $this->retention_amount,
          'retention_released_at' => now(),
          'status' => $this->paid_amount + $this->retention_amount >= $this->total ? 'Paid' : 'Partial Paid',
        ]);
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function mergeInvoices($invoices, $deleteMerged = true): void
  {
    try {
      DB::transaction(function () use ($invoices, $deleteMerged) {
        $invoices->each(function ($invoice) use ($deleteMerged) {

          $invoice->items->each(function ($item) {
            // if the invoice to be merged is using summary tax then delete the taxes of the items
            if ($this->is_summary_tax) {
              $item->taxes()->delete();
            }

            $item->update(['invoice_id' => $this->id]);

            if (!$this->is_summary_tax) {
              $item->taxes()->update(['invoice_id' => $this->id]);
            }
          });

          if ($deleteMerged)
            $invoice->delete();
          else {
            $invoice->update(['status' => 'Cancelled']);
            $invoice->updateTaxAmount();
          }
        });

        $this->updateTaxAmount();
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function uploadedDocs()
  {
    return $this->morphMany(UploadedKycDoc::class, 'doc_requestable');
  }

  public function requestedDocs()
  {
    return KycDocument::where('status', 1) // active
      ->where('workflow', 'Invoice Required Docs') // workflow
      ->where(function ($q) { // filter by invoice type')
        $q->where('invoice_type', 'Both')
          ->orWhere('invoice_type', $this->type)
          ->orWhereNull('invoice_type');
      })
      ->whereIn('client_type', array_merge(['Both'], ($this->contract->assignable instanceof Company ?  [$this->contract->assignable->type] : []))) // filter by client type
      ->where(function ($q) { // filter by contract
        $q->whereHas('contracts', function ($q) {
          $q->where('contracts.id', $this->contract_id);
        })->orHas('contracts', '=', 0);
      })
      ->where(function ($q) { // filter by contract type
        $q->when($this->contract->type_id, function ($q) {
          $q->whereHas('contractTypes', function ($q) {
            $q->where('contract_types.id', $this->contract->type_id);
          })->orHas('contractTypes', '=', 0);
        });
      })
      ->where(function ($q) { // filter by contract category
        $q->when($this->contract->category_id, function ($q) {
          $q->whereHas('contractCategories', function ($q) {
            $q->where('contract_categories.id', $this->contract->category_id);
          })->orHas('contractCategories', '=', 0);
        });
      })
      ->where(function ($q) { // required_at && required_at_type
        $q->whereNull('required_at') // if required_at is null then at any time this is required
          // if required_at is not null then check the required_at_type
          ->orWhere(function ($q) {
            $q->where('required_at', '>=', today())
              ->where('required_at_type', 'Before');
          })
          ->orWhere(function ($q) {
            $q->where('required_at', '<=', today())
              ->where('required_at_type', 'After');
          })
          ->orWhere(function ($q) {
            $q->where('required_at', today())
              ->where('required_at_type', 'On');
          });
      });
  }

  public function pendingDocs()
  {
    return $this->requestedDocs()
      ->whereDoesntHave('uploadedDocs', function ($q) { // filter by uploaded docs
        $q->where('doc_requestable_id', $this->id)
          ->where('doc_requestable_type', $this::class)
          ->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', today());
          });
      });
  }

  public function attachPhasesWithTax(array $phase_ids): bool
  {
    DB::beginTransaction();
    try {
      $pivot_amounts = ContractPhase::whereIn('id', $phase_ids)
        ->where('contract_id', $this->contract_id)
        ->has('addedAsInvoiceItem', 0)
        ->with('taxes')
        ->get();

      // formate data for pivot table
      $data = [];
      foreach ($phase_ids as $phase) {
        $data[$phase] = [
          'amount' => $pivot_amounts->where('id', $phase)->first()->getRawOriginal('estimated_cost')
        ]; // convert to cents manually, setter is not working for pivot table
      }

      $this->phases()->syncWithoutDetaching($data);

      foreach ($pivot_amounts as $phase) {
        $invPhase = $this->items()->where('invoiceable_id', $phase->id)->first();

        foreach ($phase->taxes as $tax) {
          $invPhase->taxes()->attach($tax->id, ['amount' => $tax->pivot->amount, 'type' => $tax->pivot->type, 'invoice_id' => $this->id]);
        }

        $invPhase->updateTaxAmount();
      }

      $this->updateTaxAmount();

      DB::commit();
      return true;
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
  }

  /**
   * Get the downpayment remaining amount which is not deducted (not setteled in any other invoice) from this invoice.
   */

  public function downpaymentAmountRemaining()
  {
    if ($this->type == 'Regular') {
      return 0;
    }

    return $this->total - $this->pivotSetteledDownpaymentInvoices()->sum('amount') / 1000;
  }

  /**
   * Get pivot table invoices which are using this invoice' amount as downpayment.
   */

  public function pivotSetteledDownpaymentInvoices(): HasMany
  {
    return $this->hasMany(InvoiceDownpayment::class, 'downpayment_id', 'id');
  }

  /**
   * Get pivot table invoices .
   */

  public function pivotDownpaymentInvoices(): HasMany
  {
    return $this->hasMany(InvoiceDownpayment::class, 'invoice_id', 'id');
  }

  /**
   * Get the downpayments being deducted from this invoice.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function downPayments(): BelongsToMany
  {
    return $this->belongsToMany(Invoice::class, 'invoice_downpayments', 'invoice_id', 'downpayment_id')->withPivot('is_percentage', 'amount', 'percentage', 'is_after_tax');
  }

  /**
   * Get the invoices being paid by this downpayment.
   */
  public function downPaymentOf(): BelongsToMany
  {
    return $this->belongsToMany(Invoice::class, 'invoice_downpayments', 'downpayment_id', 'invoice_id')->withPivot('is_percentage', 'amount', 'percentage', 'is_after_tax');
  }

  /**
   * Get the downpayment invoices which can be deducted from this invoice.
   */
  public function deductableDownpayments(): HasMany
  {
    return $this->hasMany(Invoice::class, 'contract_id', 'contract_id')->where('type', 'Down Payment')->where('status', 'Paid');
  }

  /**
   * Get the amount which can be paid against this invoice.
   */
  public function payableAmount()
  {
    return $this->total - $this->paid_amount - $this->retention_amount - $this->downpayment_amount;
  }
}
