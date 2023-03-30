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

use Illuminate\Support\Arr;
use App\Innoclapps\Models\Media;
use App\Innoclapps\Models\PendingMedia;
use App\Innoclapps\Repository\AppRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Innoclapps\Contracts\Repositories\MediaRepository;
use App\Innoclapps\Contracts\Repositories\PendingMediaRepository;

class PendingMediaRepositoryEloquent extends AppRepository implements PendingMediaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return PendingMedia::class;
    }

    /**
     * Get pending media by a given draft id
     */
    public function getByDraftId(string|int $draftId) : Collection
    {
        return $this->with('attachment')->findWhere(['draft_id' => $draftId]);
    }

    /**
     * Get pending media by given token(s)
     */
    public function getByToken(array|string $token) : Collection
    {
        return $this->with('attachment')
            ->whereHas('attachment', function ($query) use ($token) {
                return $query->whereIn('token', Arr::wrap($token));
            })->get();
    }

    /**
     * Mark a given media as pending
     */
    public function mark(Media $media, string $draftId) : PendingMedia
    {
        return $this->create(['media_id' => $media->id, 'draft_id' => $draftId]);
    }

    /**
     * Purge mending media by given media
     */
    public function purge(PendingMedia|int $media) : bool
    {
        $pendingMedia = $media instanceof PendingMedia ? $media : $this->find($media);
        app(MediaRepository::class)->delete($pendingMedia->attachment->id);

        return $this->delete($pendingMedia->id);
    }
}
