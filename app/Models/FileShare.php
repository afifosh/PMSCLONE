<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
    use HasFactory;

    protected $fillable = ['rfp_file_id', 'user_id', 'permission', 'expires_at'];

    public const Permissions = [
        'view' => 'View',
        'edit' => 'Edit',
        'comment' => 'Comment',
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
}
