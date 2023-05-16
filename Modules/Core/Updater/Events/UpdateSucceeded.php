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

namespace Modules\Core\Updater\Events;

use Modules\Core\Updater\Release;

class UpdateSucceeded
{
    /**
     * Initialize new UpdateSucceeded instance.
     */
    public function __construct(protected Release $release)
    {
    }

    /**
     * Get the new version.
     */
    public function getVersionUpdatedTo(): string
    {
        return $this->release->getVersion();
    }
}
