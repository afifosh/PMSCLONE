<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractNotifiableUser extends Model
{
  use HasFactory;

  protected $fillable = [
    'contract_id',
    'admin_id',
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}
