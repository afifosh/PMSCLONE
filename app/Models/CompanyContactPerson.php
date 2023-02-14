<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Avatar;

class CompanyContactPerson extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'company_id'];

    protected $table = 'company_contact_persons';

    public function getAvatarAttribute($value)
    {
      if(!$value)
      return Avatar::create($this->full_name)->toBase64();
      return $value;
    }

    public function getFullNameAttribute()
    {
      return ucwords($this->first_name. ' ' . $this->last_name);
    }

    public function invitations()
    {
      return $this->hasMany(CompanyInvitation::class, 'invited_person_id', 'id');
    }

    public function company()
    {
      return $this->belongsTo(Company::class);
    }
}
