<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTax extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'invoice_item_id',
    'tax_id',
    'amount'
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function invoiceItem()
  {
    return $this->belongsTo(InvoiceItem::class);
  }

  public function tax()
  {
    return $this->belongsTo(Tax::class);
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
