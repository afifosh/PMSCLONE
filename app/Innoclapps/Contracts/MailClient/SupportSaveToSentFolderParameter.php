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

namespace App\Innoclapps\Contracts\MailClient;

interface SupportSaveToSentFolderParameter
{
    /**
     * Indicates whether the message should be saved
     * to the sent folder after it's sent
     *
     * In most cases, this is valid for new mails not for replies
     *
     * @param boolean $value
     *
     * @return static
     */
    public function saveToSentFolder($value);
}
