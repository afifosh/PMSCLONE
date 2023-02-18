<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission;

class Module extends BaseModel
{
    use HasFactory;

    public function permissions()
    {
      return $this->hasMany(Permission::class);
    }
}
