<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'invoiceable_type',
    'invoiceable_id',
    'amount',
    'description',
  ];

  protected $casts = [
    // 'amount' => 'integer',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  // public function invoice()
  // {
  //   return $this->belongsTo(Invoice::class);
  // }

  public function invoiceable()
  {
    return $this->morphTo();
  }
}
