<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class InvoicePayment extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'transaction_id',
    'payment_date',
    'amount',
    'note',
  ];

  protected $casts = [
    'payment_date' => 'date',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function getAmountAttribute($value)
  {
    return $value / 100;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = round($value * 100, 0);
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
}
