<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFPFileLog extends Model
{
    use HasFactory;

    protected $table = 'rfp_file_logs';

    protected $fillable = ['file_id', 'log', 'actioner_id', 'actioner_type'];

    public function actioner()
    {
        return $this->morphTo('actioner', 'actioner_type', 'actioner_id');
    }

    public function file()
    {
      return $this->belongsTo(RFPFile::class, 'file_id', 'id');
    }
}
