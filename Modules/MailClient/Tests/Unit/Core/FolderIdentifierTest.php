<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Tests\Unit\Core;

use Modules\MailClient\Client\FolderIdentifier;
use Tests\TestCase;

class FolderIdentifierTest extends TestCase
{
    public function test_folder_identifier()
    {
        $identifier = new FolderIdentifier('id', 'INBOX');

        $this->assertSame('id', $identifier->key);
        $this->assertSame('INBOX', $identifier->value);
    }
}
