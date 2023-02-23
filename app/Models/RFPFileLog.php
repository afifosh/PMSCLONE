<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFPFileLog extends Model
{
    use HasFactory;

    protected $table = 'rfp_file_logs';

    protected $fillable = ['file_id', 'log', 'actioner_id', 'actioner_type'];

    public function scopeMine($query)
    {
      if(auth('admin')->check() && auth('admin')->id() == 1){
        return $query;
      }
      return $query->whereHas('file', function($q){
        $q->mine();
      });
    }

    public function scopeApplyRequestFilters($query)
    {
      $query->when(request('filter_files') && !empty(request('filter_files')) && is_array(request('filter_files')), function($q){
        $q->whereIn('file_id', request('filter_files'));
      });
      $query->when(request()->has('filter_actioner'), function($q){
        $q->whereIn('actioner_id', request('filter_actioner'));
      });
      $query->when(request()->has('filter_date_range'), function($q){
        $date = explode(' to ', request('filter_date_range'));
        if(count($date) == 2){
          $q->whereBetween('rfp_file_logs.created_at', $date);
        }elseif(!empty($date[0]) && count($date) == 1){
          $q->whereDate('rfp_file_logs.created_at', $date[0]);
        }
      });
    }

    public function actioner()
    {
        return $this->morphTo('actioner', 'actioner_type', 'actioner_id');
    }

    public function file()
    {
      return $this->belongsTo(RFPFile::class, 'file_id', 'id');
    }
}
