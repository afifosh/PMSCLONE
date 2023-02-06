<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInvitation extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'valid_till', 'role_id'];

    public function contactPerson()
    {
      return $this->belongsTo(CompanyContactPerson::class, 'invited_person_id', 'id');
    }
}
