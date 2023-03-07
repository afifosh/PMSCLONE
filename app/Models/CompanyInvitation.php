<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyInvitation extends BaseModel
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

    public function logs()
    {
      return $this->morphMany(TimelineLog::class, 'logable', 'logable_type', 'logable_id');
    }

    public function createLog($log, $data = [])
    {
      $actioner = ['actioner_id' => null, 'actioner_type' => null];
      if(auth()->check()){
        $actioner['actioner_id'] = auth()->id();
        $actioner['actioner_type'] = auth()->user()::class;
        $data['ip'] = request()->ip();
      }
      return $this->logs()->create(['log' => $log, 'data' => $data,] + $actioner);
    }
}
