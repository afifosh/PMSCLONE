<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDesignation extends BaseModel
{
    use HasFactory;

    public const DT_ID = 'partner-com-designations-dataTable';

    protected $fillable = ['name', 'department_id'];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function department()
    {
      return $this->belongsTo(CompanyDepartment::class, 'department_id', 'id');
    }
}
