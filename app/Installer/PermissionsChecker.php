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

namespace App\Installer;

class PermissionsChecker
{
    protected array $results = [
        'results' => [],
    ];

    /**
     * Check for the folders permissions.
     */
    public function check(): array
    {
        $folders = config('installer.permissions');

        foreach ($folders as $folder => $permission) {
            if (! ($this->getPermission($folder) >= $permission)) {
                $this->addFileAndSetErrors($folder, $permission, false);
            } else {
                $this->addFile($folder, $permission, true);
            }
        }

        return $this->results;
    }

    /**
     * Get a folder permission.
     */
    private function getPermission(string $folder): string
    {
        return substr(sprintf('%o', fileperms(base_path($folder))), -4);
    }

    /**
     * Add the file to the list of results.
     */
    private function addFile(string $folder, string $permission, bool $isSet): void
    {
        array_push($this->results['results'], [
            'folder' => $folder,
            'permission' => $permission,
            'isSet' => $isSet,
        ]);
    }

    /**
     * Add the file and set the errors.
     */
    private function addFileAndSetErrors(string $folder, string $permission, bool $isSet)
    {
        $this->addFile($folder, $permission, $isSet);

        $this->results['errors'] = true;
    }
}
