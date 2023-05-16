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

namespace Modules\Core\Database\State;

use Modules\Core\Facades\MailableTemplates;
use ReflectionMethod;

class EnsureMailableTemplatesArePresent
{
    public function __invoke()
    {
        if (! MailableTemplates::requiresSeeding()) {
            return;
        }

        $mailables = MailableTemplates::get();

        foreach ($mailables as $mailable) {
            $mailable = new ReflectionMethod($mailable, 'seed');

            $mailable->invoke(null);
        }
    }
}
