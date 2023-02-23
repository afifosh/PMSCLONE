<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RFPDraft extends BaseModel
{
  use HasFactory;

  protected $table = 'rfp_drafts';

  public const DT_ID = 'rfps_dataTable';

  protected $fillable = ['name', 'program_id', 'description'];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function scopeMine($query){
    if(auth('admin')->check() && auth('admin')->id() == 1){
      return $query;
    }
    return $query->whereHas('program', function($q){
      $q->mine();
    });
  }

  public function program()
  {
    return $this->belongsTo(Program::class);
  }

  public function files()
  {
    return $this->hasMany(RFPFile::class, 'rfp_id', 'id');
  }

  public function files_withTrashed()
  {
    return $this->hasMany(RFPFile::class, 'rfp_id', 'id')->withTrashed();
  }

  public function fileLogs()
  {
    return $this->hasManyThrough(RFPFileLog::class, RFPFile::class, 'rfp_id', 'file_id', 'id', 'id')->withTrashedParents();
  }
}
