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

class FailedToExtractZipException extends UpdaterException
{
    /**
     * Initialize new FailedToExtractZipException instance
     *
     * @param string $filePath
     * @param integer $code
     * @param \Exception|null $previous
     */
    public function __construct($filePath = '', $code = 0, Exception $previous = null)
    {
        parent::__construct('Failed to extract zip file. [' . $this->filePath . ']', 500, $previous);
    }
}
