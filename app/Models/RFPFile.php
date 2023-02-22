<?php

namespace App\Models;

use App\Exceptions\OperationFailedException;
use App\Models\Scopes\NotTrashedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class RFPFile extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = "rfp_files";

    protected $fillable = ['rfp_id', 'uploaded_by', 'trashed_at', 'title', 'file', 'mime_type', 'extension', 'deleted_at'];

    public const TRASH_PATH = '/trash/draft-files/';
    public const DEL_PATH = '/deleted/draft-files/';

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

    public function scopeWithTrashCheck($query)
    {
      $query->when(auth()->id() == 1, function($q){
        return $q->withTrashed();
      });
    }

    public function scopeWithBin($query)
    {
      return $query->withoutGlobalScope(NotTrashedScope::class);
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
      return $query->when(auth()->id() != 1, function($q){
        return $q->whereHas('rfp', function($q){
          $q->mine();
        });
      });
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new NotTrashedScope());
    }

    public function deleteForcefully($path){
      if(Storage::exists($path))
      Storage::delete($path);
      Storage::deleteDirectory($path.'-his');
      $this->forceDelete();
    }

    public function curFilePath()
    {
      if($this->deleted_at){
        return $this::DEL_PATH.$this->file;
      }elseif($this->trashed_at){
        return $this::TRASH_PATH.$this->file;
      }
      return $this->file;
    }

    public function curVerPath($version)
    {
      if(getFileVersion(getHistoryDir(getStoragePath($this->curFilePath()))) == $version){
        return $this->curFilePath();
      }elseif(getFileVersion(getHistoryDir(getStoragePath($this->curFilePath()))) > $version){
        return $this->curFilePath().'-hist/'.$version."/prev.".$this->extension;
      }
      throw  new OperationFailedException('File Not Found', \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
    }

    public function scopeMineOrShared($query)
    {
      return $query->when(auth()->id() != 1, function($q){
        return $q->whereHas('rfp', function($q){
          $q->mine();
        })->orWhereHas('shares', function($q){
          $q->where('user_id', auth()->id())->where(function($q){
            $q->where('expires_at', '>=', now())->orWhereNull('expires_at');
          });
        });
      });
    }

    public function scopeAvailableShared($query)
    {
      return $query->when(auth()->id() != 1, function($q){
        return $q->whereHas('shares', function($q){
          $q->where('user_id', auth()->id())->where(function($q){
            $q->where('expires_at', '>=', now())->orWhereNull('expires_at');
          });
        });
      });
    }

    public function shares()
    {
      return $this->hasMany(FileShare::class, 'rfp_file_id', 'id');
    }

    public function getMode()
    {
      return $this->getPermission() != 'view' ? 'edit' : 'view';
    }

    public function getPermission()
    {
      return @$this->shares()->where('user_id', auth()->id())->first()->permission ?? 'edit';
    }

    public function hasPermission($permission)
    {
      if(auth()->id() == 1 || $permission == 'view'){
        return true;
      }
      $userPermission = @$this->shares()->where('user_id', auth()->id())->first()->permission;
      if($userPermission){
        switch ($permission) {
          case 'comment':
            return $userPermission == 'comment';
            break;
          case 'edit':
            return $userPermission == 'edit';
            break;
          default:
            return false;
            break;
        }
      }
      return false;
    }
}
