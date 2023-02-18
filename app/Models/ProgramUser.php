<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramUser extends BaseModel
{
    use HasFactory;

    public const DT_ID = 'Program-Users-DataTable';

    protected $table = 'programs_users';

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function scopeOfProgram($query, $program)
    {
      if(!($program instanceof Program))
      {
        $program = Program::find($program);
      }

      return $query->where('program_id', $program->id)->orWhere('program_id', $program->parent_id);
    }

    public function user()
    {
      return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function addedBy()
    {
      return $this->belongsTo(Admin::class, 'added_by', 'id');
    }
}
