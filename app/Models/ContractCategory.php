<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractCategory extends Model
{
  use HasFactory;

  protected $fillable = ['name'];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contracts(): HasMany
  {
    return $this->hasMany(Contract::class, 'category_id');
  }
}
