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

namespace Modules\Core\Tests\Feature;

use Tests\TestCase;

class FilePermissionErrorTest extends TestCase
{
    public function test_file_permissions_can_be_viewed()
    {
        $this->signIn();

        $this->get('/errors/permissions')->assertOk();
    }
}
