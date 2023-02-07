<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCompany extends Model
{
    use HasFactory;

    public const DT_ID = 'partner-companies-dataTable';

    protected $fillable = ['name', 'website', 'phone'];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function departments()
    {
      return $this->hasMany(CompanyDepartment::class, 'company_id', 'id');
    }
}
