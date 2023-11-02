<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reviewable_id', 'reviewable_type', 'reviewed_at'];
      
    public function reviewable()
    {
        return $this->morphTo();
    }

    // Adding this relationship
    public function user() 
    {
        return $this->belongsTo(Admin::class);
    }    
}
