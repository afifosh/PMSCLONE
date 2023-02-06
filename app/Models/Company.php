<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  use HasFactory, HasEnum;

  public const DT_ID = 'companies_datatable';

  protected $fillable = ['name', 'email', 'status', 'added_by'];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

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
