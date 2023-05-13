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

namespace Modules\MailClient\Tests\Unit;

use Modules\MailClient\Models\PredefinedMailTemplate;
use Modules\Users\Models\User;
use Tests\TestCase;

class PredefinedMailTemplateTest extends TestCase
{
    public function test_predefined_mail_template_has_user()
    {
        $template = PredefinedMailTemplate::factory()->for(User::factory())->create();

        $this->assertInstanceOf(User::class, $template->user);
    }
}
