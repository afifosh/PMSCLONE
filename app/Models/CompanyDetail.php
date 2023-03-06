<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
    use HasFactory, Tenantable;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
