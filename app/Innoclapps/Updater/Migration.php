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

use SplFileInfo;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Migrations\Migrator;

class Migration
{
    /**
     * Initialize new Migration instance
     *
     * @param \Illuminate\Database\Migrations\Migrator $migratior
     */
    public function __construct(protected Migrator $migrator)
    {
    }

    /**
     * Get all of the updates patchers
     *
     * @return \Illuminate\Support\Collection
     */
    public function patchers()
    {
        return collect((new Filesystem)->files(base_path('patchers')))
            ->filter(
                fn (SplFileInfo $file) => str_ends_with($file->getRealPath(), '.php') &&
                     str_starts_with($file->getFilename(), 'Update')
            )
            ->values()
            ->map(fn (SplFileInfo $file) => (new Filesystem)->getRequire($file->getRealPath()))
            ->sortBy(
                fn ($patch) => $patch->version()
            )
            ->values();
    }

    /**
     * Check whether the application requires migrations to be run
     *
     * @return boolean
     */
    public function needed() : bool
    {
        $ran = $this->migrator->getRepository()->getRan();
        $all = $this->getAllMigrationFiles();

        if (count($all) > 0) {
            return count($all) > count($ran);
        }

        return false;
    }

    /**
     * Get an array of all of the migration files.
     *
     * @return array
     */
    protected function getAllMigrationFiles() : array
    {
        return $this->migrator->getMigrationFiles($this->getMigrationPaths());
    }

    /**
     * Get all of the migration paths.
     *
     * @return array
     */
    protected function getMigrationPaths() : array
    {
        return array_merge(
            $this->migrator->paths(),
            [$this->getMigrationPath()]
        );
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath() : string
    {
        return database_path('migrations');
    }
}
