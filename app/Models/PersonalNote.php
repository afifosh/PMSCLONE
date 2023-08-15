<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalNote extends Model
{
  use HasFactory;

  protected $fillable = [
    'note_tag_id', // 'note_tag_id' is the foreign key of 'id' column of 'note_tags' table.
    'user_type',
    'user_id',
    'title',
    'description',
  ];

  public function user()
  {
    return $this->morphTo();
  }

  public function tag()
  {
    return $this->belongsTo(NoteTag::class, 'note_tag_id');
  }
}
