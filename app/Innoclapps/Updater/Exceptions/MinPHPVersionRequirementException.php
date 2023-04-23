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

namespace App\Innoclapps\Updater\Exceptions;

use Exception;

class MinPHPVersionRequirementException extends UpdaterException
{
    /**
     * Initialize new LicenseNotActiveException instance
     *
     * @param string $message
     * @param integer $code
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct(
            'Your PHP version does not met the required PHP version for the release you want to update your installation.',
            400,
            $previous
        );
    }
}
