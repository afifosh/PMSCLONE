<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    public const DT_ID = 'programs-dataTable';

    protected $fillable = ['parent_id', 'name', 'image', 'program_code', 'description'];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function users()
    {
      return $this->belongsToMany(Admin::class, ProgramUser::class, 'program_id', 'admin_id')->withTimestamps();
    }

    public function parent()
    {
      return $this->belongsTo(Program::class, 'parent_id', 'id');
    }
}
