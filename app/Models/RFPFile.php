<?php

namespace App\Models;

use App\Models\Scopes\NotTrashedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RFPFile extends BaseModel
{
    use HasFactory;

    protected $table = "rfp_files";

    protected $fillable = ['rfp_id', 'uploaded_by', 'trashed_at', 'title', 'file', 'mime_type', 'extension'];

    public function rfp()
    {
      return $this->belongsTo(RFPDraft::class, 'rfp_id', 'id');
    }

    public function is_editable()
    {
      return (bool) in_array($this->extension, config('onlyoffice.supported_files'));
    }

    public function logs()
    {
      return $this->hasMany(RFPFileLog::class, 'file_id', 'id')->latest();
    }

    public function createLog($log)
    {
      return $this->logs()->create(['log' => $log, 'actioner_id' => auth()->id(), 'actioner_type' => auth()->user()::class]);
    }

    public function scopeFilter($query, $filter)
    {
      // $query->when($filter == 'documents', function($q){
      //   return $q->where('mime_type', 'like', 'application/%');
      // });
      $query->when($filter == 'trash', function($q){
        return $q->withoutGlobalScope(NotTrashedScope::class)->whereNotNull('trashed_at');
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

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new NotTrashedScope());
    }
}
