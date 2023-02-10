<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInvitation extends Model
{
    use HasFactory;

    public const DT_ID = 'company-invitations-dataTable';

    protected $fillable = ['token', 'valid_till', 'role_id', 'status'];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function contactPerson()
    {
      return $this->belongsTo(CompanyContactPerson::class, 'invited_person_id', 'id');
    }

    public function role()
    {
      return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
