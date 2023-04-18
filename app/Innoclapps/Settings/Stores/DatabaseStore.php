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

namespace App\Innoclapps\Settings\Stores;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\Innoclapps\Settings\Utilities\Arr;
use App\Innoclapps\Models\Setting as SettingModel;

/**
 * @codeCoverageIgnore
 * NOT USED IN TESTS
 */
class DatabaseStore extends AbstractStore
{
    /**
     * The eloquent model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The key column name to query from.
     *
     * @var string
     */
    protected $keyColumn;

    /**
     * The value column name to query from.
     *
     * @var string
     */
    protected $valueColumn;

    /**
     * Any query constraints that should be applied.
     *
     * @var \Closure|null
     */
    protected $queryConstraint;

    /**
     * Any extra columns that should be added to the rows.
     *
     * @var array
     */
    protected $extraColumns = [];

    /**
     * Fire the post options to customize the store.
     *
     * @param array $options
     */
    protected function postOptions(array $options)
    {
        $this->model = $this->app->make(
            Arr::get($options, 'model', SettingModel::class)
        );

        $this->setTable(Arr::get($options, 'table', 'settings'));
        $this->setKeyColumn(Arr::get($options, 'columns.key', 'key'));
        $this->setValueColumn(Arr::get($options, 'columns.value', 'value'));
    }

    /**
     * Set the db connection to query from.
     *
     * @param string $name
     *
     * @return static
     */
    public function setConnection($name)
    {
        $this->model->setConnection($name);

        return $this;
    }

    /**
     * Set the table to query from.
     *
     * @param string $name
     *
     * @return static
     */
    public function setTable($name)
    {
        $this->model->setTable($name);

        return $this;
    }

    /**
     * Set the key column name to query from.
     *
     * @param string $name
     *
     * @return static
     */
    public function setKeyColumn($name)
    {
        $this->keyColumn = $name;

        return $this;
    }

    /**
     * Set the value column name to query from.
     *
     * @param string $name
     *
     * @return static
     */
    public function setValueColumn($name)
    {
        $this->valueColumn = $name;

        return $this;
    }

    /**
     * Set the query constraint.
     *
     * @param \Closure $callback
     *
     * @return static
     */
    public function setConstraint(Closure $callback)
    {
        $this->resetLoaded();

        $this->queryConstraint = $callback;

        return $this;
    }

    /**
     * Set extra columns to be added to the rows.
     *
     * @param array $columns
     *
     * @return static
     */
    public function setExtraColumns(array $columns)
    {
        $this->resetLoaded();

        $this->extraColumns = $columns;

        return $this;
    }

    /**
     * Unset a key in the settings data.
     *
     * @param string $key
     *
     * @return static
     */
    public function forget(string $key) : static
    {
        parent::forget($key);

        // because the database store cannot store empty arrays, remove empty
        // arrays to keep data consistent before and after saving
        $segments = explode('.', $key);
        array_pop($segments);

        while (! empty($segments)) {
            $segment = implode('.', $segments);

            // non-empty array - exit out of the loop
            if ($this->get($segment)) {
                break;
            }

            // remove the empty array and move on to the next segment
            $this->forget($segment);
            array_pop($segments);
        }

        return $this;
    }

    /**
     * Read the data from the store.
     *
     * @return array
     */
    protected function read() : array
    {
        return $this->newQuery()
            ->pluck($this->valueColumn, $this->keyColumn)
            ->toArray();
    }

    /**
     * Write the data into the store.
     *
     * @param array $data
     */
    protected function write(array $data) : void
    {
        $changes = $this->getChanges($data);

        $this->syncUpdated($changes['updated']);
        $this->syncInserted($changes['inserted']);
        $this->syncDeleted($changes['deleted']);
    }

    /**
     * Create a new query builder instance.
     *
     * @param $insert  bool
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newQuery(bool $insert = false) : Builder
    {
        $query = $this->model
            ->newQuery()
            ->unless($insert, function (Builder $q) : void {
                $q->where($this->extraColumns);
            });

        if ($this->hasQueryConstraint()) {
            $callback = $this->queryConstraint;
            $callback($query, $insert);
        }

        return $query;
    }

    /**
     * Transforms settings data into an array ready to be inserted into the database.
     * Call array_dot on a multidimensional array before passing it into this method!
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareInsertData(array $data) : array
    {
        $dbData       = [];
        $extraColumns = $this->extraColumns ? $this->extraColumns : [];

        foreach ($data as $key => $value) {
            $dbData[] = array_merge($extraColumns, [
                $this->keyColumn   => $key,
                $this->valueColumn => $value,
            ]);
        }

        return $dbData;
    }

    /**
     * Check if the query constraint exists.
     *
     * @return boolean
     */
    protected function hasQueryConstraint() : bool
    {
        return ! is_null($this->queryConstraint) && is_callable($this->queryConstraint);
    }

    /**
     * Get the changed settings data.
     *
     * @param array $data
     *
     * @return array
     */
    protected function getChanges(array $data) : array
    {
        $changes = [
            'inserted' => Arr::dot($data),
            'updated'  => [],
            'deleted'  => [],
        ];

        foreach ($this->newQuery()->pluck($this->keyColumn) as $key) {
            if (Arr::has($changes['inserted'], $key)) {
                $changes['updated'][$key] = $changes['inserted'][$key];
            } else {
                $changes['deleted'][] = $key;
            }

            Arr::forget($changes['inserted'], $key);
        }

        return $changes;
    }

    /**
     * Sync the updated records.
     *
     * @param array $updated
     */
    protected function syncUpdated(array $updated) : void
    {
        foreach ($updated as $key => $value) {
            $this->newQuery()
                ->where($this->keyColumn, '=', $key)
                ->update([$this->valueColumn => $value]);
        }
    }

    /**
     * Sync the inserted records.
     *
     * @param array $inserted
     */
    protected function syncInserted(array $inserted) : void
    {
        if (! empty($inserted)) {
            $this->newQuery(true)->insert(
                $this->prepareInsertData($inserted)
            );
        }
    }

    /**
     * Sync the deleted records.
     *
     * @param array $deleted
     */
    protected function syncDeleted(array $deleted) : void
    {
        if (! empty($deleted)) {
            $this->newQuery()->whereIn($this->keyColumn, $deleted)->delete();
        }
    }
}
