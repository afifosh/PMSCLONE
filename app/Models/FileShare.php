<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
  use HasFactory;

  protected $fillable = ['rfp_file_id', 'user_id', 'shared_by', 'permission', 'expires_at', 'revoked_by'];

  public const Permissions = [
    'view' => 'View',
    'edit' => 'Edit',
    'comment' => 'Comment',
  ];

  public const Statuses = [
    'active' => 'Active',
    'revoked' => 'Revoked',
    'expired' => 'Expired',
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
    'expires_at' => 'datetime:d M, Y',
  ];

  public function file()
  {
    return $this->belongsTo(RFPFile::class, 'rfp_file_id', 'id');
  }

  public function user()
  {
    return $this->belongsTo(Admin::class, 'user_id', 'id');
  }

  public function sharedBy()
  {
    return $this->belongsTo(Admin::class, 'shared_by', 'id');
  }

  public function scopeApplyRequestFilters($query)
  {
    $query->when(request()->has('filter_drafts'), function ($q) {
      $q->whereHas('file', function ($q) {
        $q->whereIn('rfp_id', request('filter_drafts'));
      });
    });
    $query->when(request()->has('filter_status'), function ($q) {
      if (request()->filter_status == 'active') {
        $q->where(function ($q) {
          $q->Where('expires_at', '>=', today())->orWhereNull('expires_at');
        })->whereNull('revoked_by');
      } elseif (request()->filter_status == 'revoked') {
        $q->whereNotNull('revoked_by');
      } elseif (request()->filter_status == 'expired') {
        $q->whereNull('revoked_by');
        $q->where('expires_at', '<', today());
      }
    });
    $query->when(request()->has('filter_permissions'), function ($q) {
      $q->whereIn('permission', request()->filter_permissions);
    });
    $query->when(request()->has('filter_files'), function ($q) {
      $q->whereIn('rfp_file_id', request()->filter_files);
    });
    $query->when(request()->has('filter_shared_with'), function ($q) {
      $q->whereIn('user_id', request()->filter_shared_with);
    });
    $query->when(request()->has('filter_shared_by'), function ($q) {
      $q->whereIn('shared_by', request()->filter_shared_by);
    });
  }

  public function scopeMineShared($query)
  {
    return $query->when(auth()->id() != 1, function ($q) {
      $q->where('user_id', auth()->id())->where(function ($q) {
        $q->where(function ($q) {
          $q->where('expires_at', '>=', now())->orWhereNull('expires_at');
        })->whereNull('revoked_by', false);
      });
    });
  }
}
