<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Avatar;

class Medium extends Model
{
  use HasFactory;

  protected $table = 'mediums';

  public const DT_ID = 'mediums_datatable';

  protected $fillable = [
    'name',
    'added_by',
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y'
  ];

  public function getFormattedNameAttribute()
  {
      return $this->name;
  }

  public function addedBy()
  {
    return $this->belongsTo(Admin::class, 'added_by', 'id');
  }

  public function scopeApplyRequestFilters($query)
  { 
    //
  }

  public function mediumable()
  {
      return $this->morphTo();
  }  

}
