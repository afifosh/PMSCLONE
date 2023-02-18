<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Avatar;

class Company extends BaseModel
{
  use HasFactory, HasEnum;

  public const DT_ID = 'companies_datatable';

  protected $fillable = ['name', 'email', 'status', 'added_by', 'website'];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getAvatarAttribute($value)
  {
    if(!$value)
      return Avatar::create($this->name)->toBase64();
    return $value;
  }

  public function addedBy()
  {
    return $this->belongsTo(Admin::class, 'added_by', 'id');
  }
  public function users()
  {
    return $this->hasMany(User::class);
  }

  public function contactPersons()
  {
    return $this->hasMany(CompanyContactPerson::class);
  }
}
