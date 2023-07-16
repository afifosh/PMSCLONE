<?php

namespace App\Models;

use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Media as CoreMedia;

class Media extends CoreMedia
{
  use HasFactory;
  use HasLogs;
}
