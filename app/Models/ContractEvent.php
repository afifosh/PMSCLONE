<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractEvent extends Model
{
  use HasFactory, HasEnum;

  protected $fillable = [
    'modifications',
    'event_type',
    'description',
    'admin_id'
  ];

  protected $casts = [
    'modifications' => 'array',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function actioner()
  {
    return $this->belongsTo(Admin::class, 'admin_id');
  }
}
