<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailUpdateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'old_email',
        'new_email',
    ];

    public function user()
    {
        return $this->morphTo();
    }
}
