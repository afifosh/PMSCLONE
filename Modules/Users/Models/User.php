<?php

namespace Modules\Users\Models;

use App\Models\Admin;
use Modules\Core\Concerns\HasMeta;
use Modules\Core\Contracts\Metable;

class User extends Admin implements Metable
{
  use HasMeta;
}
