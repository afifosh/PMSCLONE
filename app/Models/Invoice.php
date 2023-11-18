<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    'rounding_amount',
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
    'is_payable',
    'rounding_amount',
    'void_reason'
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
    'Void'
  ];

  const TYPES = [
    'Regular',
    'Down Payment',
    'Partial Invoice'
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

  /**
   * Has Many Invoice Items
   * @return HasMany
   */
  public function items(): HasMany
  {
    return $this->hasMany(InvoiceItem::class)->orderBy('order');
  }

  /**
   * Item which are contract phases
   */
  public function phaseItems()
  {
    return $this->items()->where('invoiceable_type', ContractPhase::class);
  }

  /**
   * Custom Invoice Items
   */
  public function customItems()
  {
    return $this->items()->where('invoiceable_type', CustomInvoiceItem::class)->with('pivotTaxes');
  }

  public function phases()
  {
    return $this->morphedByMany(ContractPhase::class, 'invoiceable', 'invoice_items', 'invoice_id', 'invoiceable_id')->withPivot('subtotal', 'description', 'total_tax_amount', 'total', 'rounding_amount')->withTimestamps();
    // return $this->belongsToMany(ContractPhase::class, 'invoice_items', 'invoice_id', 'invoiceable_id')->where('invoiceable_type', ContractPhase::class);
  }

  public function retentions()
  {
    return $this->morphedByMany(Invoice::class, 'invoiceable', 'invoice_items', 'invoice_id', 'invoiceable_id')->withPivot('amount', 'description');
  }

  public function payments()
  {
    return $this->morphMany(InvoicePayment::class, 'payable');
  }

  /**
   * All The taxes (both summary and inline) which are applied on this invoice
   * @return BelongsToMany
   */
  public function taxes(): BelongsToMany
  {
    return $this->belongsToMany(InvoiceConfig::class, 'invoice_taxes', 'invoice_id', 'tax_id')->withPivot('amount', 'type', 'invoice_item_id');
  }

  /**
   * Summary taxes which are applied on this invoice
   * @return BelongsToMany
   */
  public function summaryTaxes()
  {
    return $this->taxes()->where('invoice_taxes.invoice_item_id', null);
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

  public function getDownpaymentAmountRemainingAttribute()
  {
    return $this->downpaymentAmountRemaining();
  }

  /**
   * Recalculate subtotal, tax, downpayment, retention, adjustment, total amount
   * @return void
   */
  public function reCalculateTotal(): void
  {
    if ($this->type == 'Partial Invoice' || $this->type == 'Down Payment') {
      $subtotal = $this->items()->where('invoiceable_type', CustomInvoiceItem::class)->sum('total') / 1000;
    } else if ($this->type == 'Regular') {
      $subtotal = $this->items()->where('invoiceable_type', ContractPhase::class)->sum('total') / 1000;
    }

    // Sumary Tax Calculation. Inline Tax is settled in each item's total
    $total_tax = $this->calculateSummaryTax($subtotal);

    $total = $subtotal
      - $this->discount_amount
      + $total_tax // add total summary tax.
      + $this->adjustment_amount; // adjustment amount can be negative or positive depending on the user input

    // Retention amount is calculated after tax on total
    $retention_amount = $this->calRetentionAmount($total);

    $this->update([
      'subtotal' => $subtotal,
      'downpayment_amount' => $this->downPayments()->wherePivot('is_after_tax', 1)->sum('amount') / 1000,
      'retention_amount' => $retention_amount,
      'total' => $total,
      'total_tax' => $total_tax,
      'rounding_amount' => ($this->rounding_amount ? (floor($total) - $total) : 0),
    ]);

    $this->reCalculateTaxAuthorityInvoice();
  }

  public function reCalculateTaxAuthorityInvoice()
  {
    $ta_invoice = $this->authorityInvoice()->firstOrNew(['invoice_id' => $this->id]);
    $ta_invoice->total_tax = $this->totalAuthorityTax();
    $ta_invoice->total = $this->totalAuthorityTax();
    $ta_invoice->due_date = $this->due_date;
    $ta_invoice->save();
  }

  /**
   * Calculate summary tax
   */
  private function calculateSummaryTax($subtotal)
  {
    $fixed_tax = $this->summaryTaxes()->where('invoice_taxes.type', 'Fixed')->sum('invoice_taxes.amount') / 1000;
    $percent_tax = $this->summaryTaxes()->where('invoice_taxes.type', 'Percent')->sum('invoice_taxes.amount') / 1000;

    return ($fixed_tax + ($subtotal * $percent_tax / 100));
  }

  public function updateRetention($retention_id): void
  {
    $retenion = InvoiceConfig::where('config_type', 'Retention')->find($retention_id);
    if (!$retenion) {
      $data['retention_amount'] = 0;
      $data['retention_percentage'] = 0;
    } elseif ($retenion->type == 'Percent') {
      $data['retention_amount'] = ($this->total * $retenion->amount) / 100;
      $data['retention_percentage'] = $retenion->amount;
    } else {
      $data['retention_amount'] = $retenion->amount;
      $data['retention_percentage'] = 0;
    }

    $this->update($data + ['retention_id' => $retention_id, 'retention_name' => $retenion->name ?? null]);

    $this->reCalculateTotal();
  }

  /**
   * Calculate retention amount
   */
  private function calRetentionAmount($total)
  {
    if ($this->retention_percentage) {
      return ($total * $this->retention_percentage) / 100;
    }

    return $this->retention_amount;
  }

  public function reCalculateRetention(): void
  {
    if ($this->retention_percentage) {
      $data['retention_amount'] = ($this->total  * $this->retention_percentage) / 100;
      $this->update($data);
    }
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
    })->when(request()->notvoid, function ($q) {
      $q->where('status', '!=', 'Void');
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
            $invoice->reCalculateTotal();
          }
        });

        $this->reCalculateTotal();
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * All the documents which are uploaded against this invoice (Both global and non global)
   */
  public function uploadedDocs()
  {
    return $this->morphMany(UploadedKycDoc::class, 'doc_requestable')->where(function ($q) {
      $q->whereHas('kycDoc', function ($q) {
        $q->where('is_global', false);
      });
    })
      // Global docs which are uploaded against this invoice
      ->orWhere(function ($q) {
        $q->whereHas('kycDoc', function ($q) {
          $q->where('is_global', true);
        })
          ->whereHasMorph('docRequestable', [Invoice::class], function ($q) {
            $q->whereHas('contract', function ($q) {
              $q->where('id', $this->contract_id);
            });
          });
      });
  }

  /**
   * All the documents which are requested to upload against this invoice (Both global and non global)
   */
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

  /**
   * Documents which are requested to upload against this invoice but not uploaded yet or expired (Both global and non global)
   */
  public function pendingDocs()
  {
    return $this->requestedDocs()
      ->whereDoesntHave('uploadedDocs', function ($q) { // filter out by uploaded docs
        $q->where(function ($q) {
          $q->where('doc_requestable_id', $this->id)
            ->where('doc_requestable_type', $this::class)
            // Exclude global docs which are uploaded in any other invoice of contract of this invoice
            ->orWhere(function ($q) {
              $q->whereHasMorph('docRequestable', [Invoice::class], function ($q) {
                $q->where('contract_id', $this->contract_id);
              })->whereHas('kycDoc', function ($q) {
                $q->where('is_global', true);
              });
            });
        })
          // Exclude docs which are active and not expired
          ->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', today());
          });
      });
  }

  /**
   * Documents requested to upload against this invoice which are uploaded and not expired (Both global and non global)
   */
  public function uploadedValidDocs()
  {
    return $this->requestedDocs()
      ->whereHas('uploadedDocs', function ($q) { // has uploaded docs
        $q->where(function ($q) {
          $q->where('doc_requestable_id', $this->id)
            ->where('doc_requestable_type', $this::class)
            //global docs which are uploaded in any other invoice of contract of this invoice
            ->orWhere(function ($q) {
              $q->whereHasMorph('docRequestable', [Invoice::class], function ($q) {
                $q->where('contract_id', $this->contract_id);
              })->whereHas('kycDoc', function ($q) {
                $q->where('is_global', true);
              });
            });
        })
          // docs which are active and not expired
          ->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', today());
          });
      });
  }

  /**
   * Documents requested to upload against this invoice which are uploaded and expired (Both global and non global)
   */
  public function uploadedExpiredDocs()
  {
    return $this->requestedDocs()
      ->whereHas('uploadedDocs', function ($q) { // has uploaded docs
        $q->where(function ($q) {
          $q->where('doc_requestable_id', $this->id)
            ->where('doc_requestable_type', $this::class)
            //global docs which are uploaded in any other invoice of contract of this invoice
            ->orWhere(function ($q) {
              $q->whereHasMorph('docRequestable', [Invoice::class], function ($q) {
                $q->where('contract_id', $this->contract_id);
              })->whereHas('kycDoc', function ($q) {
                $q->where('is_global', true);
              });
            });
        })
          // docs which are active and not expired
          ->where(function ($q) {
            $q->whereNotNull('expiry_date')
              ->where('expiry_date', '<', today());
          });
      })

      // does not have valid uploaded docs
      ->whereDoesntHave('uploadedDocs', function ($q) { // filter out by uploaded docs
        $q->where(function ($q) {
          $q->where('doc_requestable_id', $this->id)
            ->where('doc_requestable_type', $this::class)
            // Exclude global docs which are uploaded in any other invoice of contract of this invoice
            ->orWhere(function ($q) {
              $q->whereHasMorph('docRequestable', [Invoice::class], function ($q) {
                $q->where('contract_id', $this->contract_id);
              })->whereHas('kycDoc', function ($q) {
                $q->where('is_global', true);
              });
            });
        })
          // Exclude docs which are active and not expired
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
        // ->has('addedAsInvoiceItem', 0)
        ->with('taxes')
        ->get();

      // formate data for pivot table
      $data = [];
      foreach ($phase_ids as $phase) {
        $data[$phase] = [
          'subtotal' => $pivot_amounts->where('id', $phase)->first()->getRawOriginal('estimated_cost'),
          'total_tax_amount' => $pivot_amounts->where('id', $phase)->first()->getRawOriginal('tax_amount'),
          // 'manual_tax_amount' => $pivot_amounts->where('id', $phase)->first()->getRawOriginal('manual_tax_amount'),
          'total' => $pivot_amounts->where('id', $phase)->first()->getRawOriginal('total_cost'),
          'rounding_amount' => $pivot_amounts->where('id', $phase)->first()->getRawOriginal('rounding_amount'),
        ]; // convert to cents manually, setter is not working for pivot table
      }

      $this->phases()->syncWithoutDetaching($data);

      foreach ($pivot_amounts as $phase) {
        $invPhase = $this->items()->where('invoiceable_id', $phase->id)->first();

        foreach ($phase->taxes as $tax) {
          $invPhase->taxes()->attach($tax->id, [
            'amount' => $tax->pivot->amount,
            'is_simple_tax' => 1,
            'calculated_amount' => $tax->pivot->calculated_amount,
            'manual_amount' => $tax->pivot->manual_amount,
            'pay_on_behalf' => $tax->pivot->pay_on_behalf,
            'is_authority_tax' => $tax->pivot->is_authority_tax,
            'type' => $tax->pivot->type,
            'invoice_id' => $this->id
          ]);
        }
      }

      $this->reCalculateTotal();

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

    return $this->total - $this->totalDeductedAmountFromThisInvoice();
  }

  /**
   * Get the downpayment amount which is deducted (setteled in any other invoice) from this invoice.
   */
  public function totalDeductedAmountFromThisInvoice()
  {
    return InvoiceDeduction::where('downpayment_id', $this->id)
      ->select([
        DB::raw('COALESCE(NULLIF(manual_amount, 0), amount) as total_amount')
      ])
      ->sum(DB::raw('COALESCE(NULLIF(manual_amount, 0), amount)')) / 1000;

    // $total = 0;
    // $deductions = InvoiceDeduction::where('downpayment_id', $this->id)->select('amount', 'manual_amount')->get();

    // foreach ($deductions as $deduction) {
    //   $total += ($deduction->manual_amount != 0 ? $deduction->manual_amount : $deduction->amount);
    // }

    // return $total;
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

  /**
   * pivot table for storing deduction information
   *
   * @return MorphOne
   */
  public function deduction()
  {
    return $this->morphOne(InvoiceDeduction::class, 'deductible');
  }

  /**
   * Total amount deducted from this invoice by downpayment deduction weather from invoice or invoice items
   */
  public function totalDeductedAmount($downpayment_id = null)
  {
    return InvoiceDeduction::where(function ($q) {
      $q->where('deductible_type', Invoice::class)
        ->where('deductible_id', $this->id);
    })->orWhere(function ($q) {
      $q->where('deductible_type', InvoiceItem::class)
        ->whereHasMorph('deductible', [InvoiceItem::class], function ($q) {
          $q->where('invoice_id', $this->id);
        });
    })
      ->when($downpayment_id, function ($q) use ($downpayment_id) {
        $q->where('downpayment_id', $downpayment_id);
      })
      ->select([
        DB::raw('COALESCE(NULLIF(manual_amount, 0), amount) as total_amount')
      ])
      ->sum(DB::raw('COALESCE(NULLIF(manual_amount, 0), amount)')) / 1000;
  }

  /**
   * Total Tax amount applied on this invoice or invoice items
   */
  public function totalAppliedTax()
  {
    return $this->total_tax + (InvoiceItem::where('invoice_id', $this->id)
      ->when($this->type == 'Partial Invoice', function ($q) {
        $q->where('invoiceable_type', CustomInvoiceItem::class);
      })
      ->when($this->type == 'Regular', function ($q) {
        $q->where('invoiceable_type', ContractPhase::class);
      })
      ->sum('total_tax_amount') / 1000);
  }

  /**
   * subtotal amount of invoice items
   */
  public function itemsSubtotalAmount()
  {
    return InvoiceItem::where('invoice_id', $this->id)
      ->when($this->type == 'Partial Invoice', function ($q) {
        $q->where('invoiceable_type', CustomInvoiceItem::class);
      })
      ->when($this->type == 'Regular', function ($q) {
        $q->where('invoiceable_type', ContractPhase::class);
      })
      ->sum('subtotal') / 1000;
  }

  public function authorityInvoice()
  {
    return $this->hasOne(AuthorityInvoice::class);
  }

  public function totalAuthorityTax()
  {
    return $this->items->sum('authority_inv_total');
  }
}
