<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core\Models;

use GeneaLabs\LaravelPivotEvents\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Date\Carbon;

class Model extends EloquentModel
{
    use PivotEventTrait;

    /**
     * The fields for the model that are searchable.
     */
    protected static array $searchableFields = [];

    /**
     * Get the model searchable fields.
     */
    public function getSearchableFields(): array
    {
        return static::$searchableFields;
    }

    /**
     * Set the model searchable fields.
     */
    public function setSearchableFields(array $fields): static
    {
        static::$searchableFields = $fields;

        return $this;
    }

    /**
     * Eager load the relations that are required for the front end response.
     */
    public function scopeWithResponseRelations(Builder $query): void
    {
    }

    /**
     * Apply order to null values to be sorted as last
     */
    public function scopeOrderByNullsLast(Builder $query, string $column, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw(
            $this->getNullsLastSql($query, $column, $direction)
        );
    }

    /**
     * Check whether the model uses the SoftDeletes trait
     */
    public function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this::class));
    }

    /**
     * Clear the guardable columns checks cache
     * Used only in unit tests after the custom fields are added
     * As Laravel caches the available columns
     */
    public static function clearGuardableCache(): void
    {
        static::$guardableColumns = [];
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value

     * @return \Modules\Core\Date\Carbon
     */
    protected function asDateTime($value)
    {
        $value = parent::asDateTime($value);

        return Carbon::instance($value);
    }

    /**
     * Get NULLS LAST SQL.
     */
    protected function getNullsLastSql(Builder $query, string $column, string $direction): string
    {
        /** @var \Illuminate\Database\MySqlConnection */
        $connection = $query->getConnection();

        // Todo, add for sqlite
        $sql = match ($connection->getDriverName()) {
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
