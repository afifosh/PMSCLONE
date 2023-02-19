<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends SpatieRole implements Auditable
{
  use \OwenIt\Auditing\Auditable;

  public const COMPANY_ADMIN_ROLE = 'Company Admin';
}
