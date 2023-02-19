<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDepartment extends BaseModel
{
    use HasFactory;

    public const DT_ID = 'partner-com-departments-dataTable';

    protected $fillable = [ 'name', 'company_id', 'head_id'];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function head()
    {
      return $this->belongsTo(Admin::class, 'head_id', 'id');
    }

    public function designations()
    {
      return $this->hasMany(CompanyDesignation::class, 'department_id', 'id');
    }

    public function company()
    {
      return $this->belongsTo(PartnerCompany::class, 'company_id', 'id');
    }

    public function subDepartments()
    {
      return $this->hasMany(CompanyDepartment::class, 'parent_id', 'id');
    }

    public function parentDepartment()
    {
      return $this->belongsTo(CompanyDepartment::class, 'parent_id', 'id');
    }
}
