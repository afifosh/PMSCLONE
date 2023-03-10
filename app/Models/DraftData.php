<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftData extends Model
{
    use HasFactory;

    protected $fillable = [
        'draftable_type',
        'draftable_id',
        'type',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function draftable()
    {
        return $this->morphTo();
    }
}
