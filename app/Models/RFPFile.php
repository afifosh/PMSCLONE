<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFPFile extends Model
{
    use HasFactory;

    protected $table = "rfp_files";

    protected $fillable = ['rfp_id', 'uploaded_by', 'title', 'file'];

    public function rfp()
    {
      return $this->belongsTo(RFPDraft::class, 'rfp_id', 'id');
    }

    public function scopeMine($query){
      if(auth('admin')->check() && auth('admin')->id() == 1){
        return $query;
      }
      return $query->whereHas('rfp', function($q){
        $q->mine();
      });
    }
}
