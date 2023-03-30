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

namespace App\Innoclapps\Google\Services;

use Google_Service_Gmail;

class History extends Service
{
    /**
     * Initialize new Service instance
     *
     * @param \Google_Client $client
     */
    public function __construct($client)
    {
        parent::__construct($client, Google_Service_Gmail::class);
    }

    /**
     * https://developers.google.com/gmail/api/v1/reference/users/history/list
     *
     * Get the Gmail account history
     *
     * @param array $params Additional params for the request
     *
     * @return \Google_Service_Gmail_History
     */
    public function get($params = [])
    {
        return $this->service->users_history->listUsersHistory('me', $params);
    }
}
