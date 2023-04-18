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

namespace App\Innoclapps\Updater\Events;

use App\Innoclapps\Updater\Release;

class UpdateSucceeded
{
    /**
     * Initialize new UpdateSucceeded instance.
     *
     * @param \App\Innoclapps\Updater\Release $release
     */
    public function __construct(protected Release $release)
    {
    }

    /**
     * Get the new version.
     *
     * @return string
     */
    public function getVersionUpdatedTo() : string
    {
        return $this->release->getVersion();
    }
}
