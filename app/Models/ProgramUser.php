<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramUser extends Model
{
    use HasFactory;

    public const DT_ID = 'Program-Users-DataTable';

    protected $table = 'programs_users';

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function user()
    {
      return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function addedBy()
    {
      return $this->belongsTo(Admin::class, 'added_by', 'id');
    }
}
