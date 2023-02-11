<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyContactPerson extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email'];

    protected $table = 'company_contact_persons';

    public function invitations()
    {
      return $this->hasMany(CompanyInvitation::class, 'invited_person_id', 'id');
    }

    public function company()
    {
      return $this->belongsTo(Company::class);
    }
}
