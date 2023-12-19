<?php

namespace App\Models;

use App\Support\LaravelBalance\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class InvoicePayment extends Model
{
  use HasFactory;

  protected $fillable = [
    'payable_type',
    'payable_id',
    'ba_trx_id', // Transaction of the payment in the account of the program
    'transaction_id',
    'payment_date',
    'amount',
    'note',
    'type'
  ];

  protected $casts = [
    'payment_date' => 'date',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function payable()
  {
    return $this->morphTo();
  }

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = moneyToInt($value);
  }

  public function contract()
  {
    return $this->hasOneThrough(Contract::class, Invoice::class, 'id', 'id', 'invoice_id', 'contract_id');
  }

  public function scopeApplyRequestFilters($q)
  {
    $q->when(request()->filter_company, function ($q) {
      $q->whereHas('invoice', function ($q) {
        $q->whereHas('contract', function ($q) {
          $q->where('company_id', request()->filter_company);
        });
      });
    })
    ->when(request()->filter_contract_category, function ($q) {
      $q->whereHas('invoice', function ($q) {
        $q->whereHas('contract', function ($q) {
          $q->where('category_id', request()->filter_contract_category);
        });
      });
    })
    ->when(request()->filter_contract, function ($q) {
      $q->whereHas('invoice', function ($q) {
        $q->where('contract_id', request()->filter_contract);
      });
    })
    ->when(request()->filter_invoice_type, function ($q) {
      $q->whereHas('invoice', function ($q) {
        $q->where('type', request()->filter_invoice_type);
      });
    })
    ->when(request()->filter_invoice, function ($q) {
      $q->where('invoice_id', request()->filter_invoice);
    });
  }

  /**
   * Transaction of the payment in the account of the program
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function accountTransaction()
  {
    return $this->belongsTo(Transaction::class, 'ba_trx_id');
  }
}
