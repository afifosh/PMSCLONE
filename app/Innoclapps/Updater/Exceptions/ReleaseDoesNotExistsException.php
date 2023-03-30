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

class ReleaseDoesNotExistsException extends UpdaterException
{
    /**
     * Initialize new ReleaseDoesNotExistsException instance
     *
     * @param string $message
     * @param integer $code
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct('The requested release does not exists in the archive.', 404, $previous);
    }
}
