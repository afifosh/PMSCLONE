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

namespace Modules\Activities\Listeners;

use Modules\Activities\Models\Calendar;
use Modules\Core\OAuth\Events\OAuthAccountDeleting;

class StopRelatedOAuthCalendars
{
    /**
     * Stop the related calendars of the OAuth account when deleting.
     */
    public function handle(OAuthAccountDeleting $event): void
    {
        $oAuthAccount = $event->account;

        if ($calendar = Calendar::with('synchronization')->where('access_token_id', $oAuthAccount->id)->first()) {
            Calendar::unguarded(function () use ($calendar) {
                $calendar->fill(['access_token_id' => null])->save();
            });

            $calendar->disableSync();
        }
    }
}
