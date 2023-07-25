<?php

namespace Modules\Chat\Queries;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class RoleDataTable
 */
class RoleDataTable
{
    /**
     * @return Builder
     */
    public function get()
    {
        return Role::with('permissions');
    }
}
