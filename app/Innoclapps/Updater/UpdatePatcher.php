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

namespace App\Innoclapps\Updater;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

abstract class UpdatePatcher
{
    /**
    * Run the patcher
    */
    abstract public function run() : void;

    /**
     * Check whether the patcher should run
     */
    abstract public function shouldRun() : bool;

    /**
     * Get the version number this patcher is intended for
     */
    public function version() : string
    {
        // 110 => 1.1.0
        return wordwrap(Str::after($this->filenameWithoutExtension(), 'Update'), 1, '.', true);
    }

    /**
     * Get the class filename without extension
     */
    protected function filenameWithoutExtension() : string
    {
        return str_replace('.php', '', basename((new ReflectionClass($this))->getFileName()));
    }

    /**
     * Get column indexes
     */
    protected function getColumnIndexes(string $table, string $column) : array
    {
        return DB::select(
            DB::raw(
                'SHOW KEYS
                FROM ' . DB::getTablePrefix() . $table . '
                WHERE Column_name="' . $column . '"'
            )
        );
    }
}
