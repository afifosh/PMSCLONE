<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps\Models;

use App\Innoclapps\Date\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelPivotEvents\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    use PivotEventTrait;

    /**
     * Apply order to null values to be sorted as last
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByNullsLast($query, $column, $direction = 'asc')
    {
        return $query->orderByRaw(
            $this->getNullsLastSql($query, $column, $direction)
        );
    }

    /**
     * Check whether the model uses the SoftDeletes trait
     *
     * @return boolean
     */
    public function usesSoftDeletes()
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this::class));
    }

    /**
     * Mark the model that will be force deleted before firing the model forceDelete method
     *
     * @return static
     */
    public function willForceDelete()
    {
        $this->forceDeleting = true;

        return $this;
    }

    /**
     * Clear the guardable columns checks cache
     * Used only in unit tests after the custom fields are added
     * As Laravel caches the available columns
     *
     * @return void
     */
    public static function clearGuardableCache()
    {
        static::$guardableColumns = [];
    }

    /**
    * Return a timestamp as DateTime object.
    *
    * @param mixed  $value
    * @return \App\Innoclapps\Date\Carbon
    */
    protected function asDateTime($value)
    {
        $value = parent::asDateTime($value);

        return Carbon::instance($value);
    }

    /**
     * Get NULLS LAST SQL.
     *
     * @param string $column
     * @param string $direction
     * @return string
     */
    protected function getNullsLastSql($query, $column, $direction)
    {
        // Todo, add for sqlite
        $sql = match ($query->getConnection()->getDriverName()) {
            'mysql', 'mariadb', 'sqlsrv' => 'CASE WHEN :column IS NULL THEN 1 ELSE 0 END, :column :direction',
            'pgsql' => ':column :direction NULLS LAST',
        };

        return str_replace(
            [':column', ':direction'],
            [$column, $direction],
            sprintf($sql, $column, $direction)
        );
    }
}
