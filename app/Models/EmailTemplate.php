<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'from',
        'created_by',
    ];

    public function langTemplates()
    {
      return $this->hasMany(EmailTemplateLang::class, 'parent_id', 'id');
    }

    public function template()
    {
        return $this->hasOne('App\Models\UserEmailTemplate', 'template_id', 'id')->where('user_id', '=', \Auth::user()->id);
    }
}
