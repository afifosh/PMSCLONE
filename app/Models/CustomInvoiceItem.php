<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomInvoiceItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'price',
    'quantity',
    'subtotal'
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getPriceAttribute($value)
  {
    return $value / 1000;
  }

  public function setPriceAttribute($value)
  {
    $this->attributes['price'] = moneyToInt($value);
  }

  public function getTotalAttribute($value)
  {
    return $value / 1000;
  }


  public function setTotalAttribute($value)
  {
    $this->attributes['total'] = moneyToInt($value);
  }

  public function invoice()
  {
    return $this->morphOne(InvoiceItem::class, 'invoiceable');
  }

  public function invoiceItem()
  {
    return $this->morphOne(InvoiceItem::class, 'invoiceable');
  }
}
