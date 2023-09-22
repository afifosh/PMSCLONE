<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
