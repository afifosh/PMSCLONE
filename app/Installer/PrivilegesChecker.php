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

use Illuminate\Database\Connection;

class PrivilegesChecker
{
    /**
     * Initialize PrivilegesChecker instance
     */
    public function __construct(protected Connection $connection)
    {
    }

    /**
     * Check the privileges
     *
     * @throws \App\Installer\PrivilegeNotGrantedException
     */
    public function check(): void
    {
        $testMethods = $this->getTesterMethods();
        $tester = new DatabaseTest($this->connection);

        foreach ($testMethods as $test) {
            $tester->{$test}();

            throw_if($tester->getLastError(), new PrivilegeNotGrantedException($tester->getLastError()));
        }
    }

    /**
     * Get the tester methods
     */
    protected function getTesterMethods(): array
    {
        return [
            // should be first as it's the most important for this test as all other tests are dropping the table
            'testDropTable',
            'testCreateTable',
            'testSelect',
            'testInsert',
            'testUpdate',
            'testDelete',
            'testAlter',
            'testIndex',
            'testReferences',
        ];
    }
}
