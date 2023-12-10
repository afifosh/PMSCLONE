<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAccessList extends Model
{
  use HasFactory;

  public const DT_ID = 'Program-Users-DataTable';

  protected $fillable = [
    'admin_id',
    'accessable_id',
    'accessable_type',
    'granted_till',
    'is_revoked',
  ];

  protected $casts = [
    'granted_till' => 'datetime:d M, Y',
    'is_revoked' => 'boolean',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  /**
   * The Admin that has this access
   */
  public function user()
  {
    return $this->belongsTo(Admin::class, 'admin_id', 'id');
  }

  /**
   * The model that this access is granted to
   */
  public function accessable()
  {
    return $this->morphTo();
  }

  public function scopeOfProgram($query, $programId)
  {
    return $query->where('accessable_id', $programId)->where('accessable_type', Program::class);
  }
}
