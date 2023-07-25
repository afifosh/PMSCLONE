<?php

namespace Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Chat\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Chat\Models\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Chat\Models\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Chat\Models\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Chat\Models\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Chat\Models\Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Chat\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Chat\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Chat\Models\Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 *
 * @property-write mixed $raw
 */
class Permission extends Model
{
    //
}
