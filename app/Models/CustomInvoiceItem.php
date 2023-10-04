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
    'total',
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
    $this->attributes['price'] = round($value * 1000);
  }

  public function getTotalAttribute($value)
  {
    return $value / 1000;
  }


  public function setTotalAttribute($value)
  {
    $this->attributes['total'] = round($value * 1000);
  }

  public function invoice()
  {
    return $this->morphOne(InvoiceItem::class, 'invoiceable');
  }
}
