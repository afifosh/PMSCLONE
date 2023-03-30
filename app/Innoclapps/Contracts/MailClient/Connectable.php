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

use App\Innoclapps\MailClient\Imap\Config;

interface Connectable
{
    /**
     * Connect to server
     *
     * @return mixed
     */
    public function connect();

    /**
     * Test the connection
     *
     * @return mixed
     */
    public function testConnection();

    /**
     * Get the connection config
     *
     * @return \App\Innoclapps\MailClient\Imap\Config
     */
    public function getConfig() : Config;
}
