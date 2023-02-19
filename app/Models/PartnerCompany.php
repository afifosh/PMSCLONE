<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Avatar;

class PartnerCompany extends BaseModel
{
    use HasFactory;

    public const DT_ID = 'partner-companies-dataTable';

    protected $fillable = ['name', 'website', 'phone'];

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

    public function departments()
    {
      return $this->hasMany(CompanyDepartment::class, 'company_id', 'id');
    }
}
