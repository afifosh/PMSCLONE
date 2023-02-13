<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PasswordHistory extends Model
{
    use HasFactory;

    /**
     * Fields that are mass assignable
     * 
     * @var array 
     */
    protected $fillable = [
        'password'
    ];

    /**
     * Morph to models User and Admin
     * 
     * @return MorphTo
     */
    public function authable()
    {
        return $this->morphTo();
    }
}
