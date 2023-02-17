<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFPFile extends Model
{
    use HasFactory;

    protected $table = "rfp_files";

    protected $fillable = ['rfp_id', 'uploaded_by', 'title', 'file', 'mime_type', 'extension'];

    public function rfp()
    {
      return $this->belongsTo(RFPDraft::class, 'rfp_id', 'id');
    }

    public function scopeFilter($query, $filter)
    {
      // $query->when($filter == 'documents', function($q){
      //   return $q->where('mime_type', 'like', 'application/%');
      // });
      $query->when($filter == 'trash', function($q){
        return $q->whereNotNull('trashed_at');
      });
      $query->when($filter == 'documents', function($q){
        return $q->where('mime_type', 'like', 'application/%')->whereNotIn('mime_type', ['application/zip', 'application/x-rar-compressed']);
      });
      $query->when($filter == 'images', function($q){
        return $q->where('mime_type', 'like', 'image/%');
      });

      $query->when($filter == 'videos', function($q){
        return $q->where('mime_type', 'like', 'video/%');
      });

      $query->when($filter == 'audios', function($q){
        return $q->where('mime_type', 'like', 'audio/%');
      });

      $query->when($filter == 'archives', function($q){
        return $q->whereIn('mime_type', ['application/zip', 'application/x-rar-compressed']);
      });

      return $query;
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
