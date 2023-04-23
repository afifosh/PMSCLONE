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

namespace App\Innoclapps\Media;

use App\Innoclapps\Contracts\Repositories\PendingMediaRepository;

class PruneStaleMediaAttachments
{
    /**
     * Prune the stale attached media from the system.
     *
     * @return void
     */
    public function __invoke()
    {
        $repository = resolve(PendingMediaRepository::class);

        $repository->orderBy('id', 'desc')
            ->with('attachment')
            ->findWhere([
            ['created_at', '<=', now()->subDays(1)],
        ])->each(function ($media) use ($repository) {
            $repository->purge($media);
        });
    }
}
