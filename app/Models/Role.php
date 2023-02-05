<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
  public const COMPANY_ADMIN_ROLE = 'Company Admin';
}
