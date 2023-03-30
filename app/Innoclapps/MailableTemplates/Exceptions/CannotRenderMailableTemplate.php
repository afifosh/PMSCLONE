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

namespace App\Innoclapps\MailableTemplates\Exceptions;

use Exception;

class CannotRenderMailableTemplate extends Exception
{
    /**
     * Throw exception
     *
     * @throws CannotRenderMailableTemplate
     *
     * @return Exception
     */
    public static function layoutDoesNotContainABodyPlaceHolder()
    {
        return new static('The layout does not contain a `{{{ mailBody }}}` placeholder');
    }
}
