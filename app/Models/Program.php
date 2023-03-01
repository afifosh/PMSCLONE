<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends BaseModel
{
    use HasFactory;

    public const DT_ID = 'programs-dataTable';

    protected $fillable = ['parent_id', 'name', 'image', 'program_code', 'description'];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function scopeMine($query){
      if(auth('admin')->check() && auth('admin')->id() == 1){
        return $query;
      }
      return $query->whereHas('users', function($q){
        return $q->where('admins.id', auth()->id());
      })->orWhereHas('parent.users', function($q){
        return $q->where('admins.id', auth()->id());
      });
    }

    public function programUsers()
    {
      return Admin::whereHas('programs', function($q){
        return $q->where('programs.id', $this->id);
      })->orWhereHas('programs', function($q){
        return $q->where('programs.id', $this->parent_id);
      })->get();
    }

    public function users()
    {
      return $this->belongsToMany(Admin::class, ProgramUser::class, 'program_id', 'admin_id')->withTimestamps();
    }

    public function parent()
    {
      return $this->belongsTo(Program::class, 'parent_id', 'id');
    }
}
