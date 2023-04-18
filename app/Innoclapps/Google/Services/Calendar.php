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

use Google_Service_Calendar;

class Calendar extends Service
{
    /**
     * Initialize new Service instance
     *
     * @param \Google_Client $client
     */
    public function __construct($client)
    {
        parent::__construct($client, Google_Service_Calendar::class);
    }

    /**
     * List all available user calendars
     *
     * @return \Google_Service_Calendar_CalendarListEntry[]
     */
    public function list()
    {
        $calendars = [];
        $nextPage  = null;

        do {
            $calendarList = $this->service->calendarList->listCalendarList([
                'pageToken' => $nextPage,
            ]);

            foreach ($calendarList->getItems() as $calendar) {
                $calendars[] = $calendar;
            }
        } while (($nextPage = $calendarList->getNextPageToken()));

        return $calendars;
    }

    /**
     * Get calendar by id
     *
     * @param string $id
     *
     * @return \Google_Service_Calendar_CalendarListEntry
     */
    public function get($id)
    {
        return $this->service->calendars->get('me', $id);
    }
}
