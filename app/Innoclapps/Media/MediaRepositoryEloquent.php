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

use App\Innoclapps\Models\Media;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Contracts\Repositories\MediaRepository;

class MediaRepositoryEloquent extends AppRepository implements MediaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Media::class;
    }

    /**
     * Find media by a given token
     *
     * @param string $token
     *
     * @return \App\Innoclapps\Models\Media|null
     */
    public function findByToken(string $token) : ?Media
    {
        return $this->findWhere(['token' => $token])->first();
    }

    /**
     *  @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * Find media by a given token
     *
     * @param string $token
     *
     * @return \App\Innoclapps\Models\Media|null
     */
    public function findByTokenOrFail(string $token) : Media
    {
        return tap($this->findByToken($token), fn ($media) => abort_unless($media, 404));
    }

    /**
     * Delete media by given token
     *
     * @param string $token
     *
     * @return boolean
     */
    public function deleteByToken(string $token)
    {
        if ($media = $this->findByToken($token)) {
            return $this->delete($media->id);
        }

        return false;
    }

    /**
     * Delete media by given tokens
     *
     * @param array $tokens
     *
     * @return void
     */
    public function deleteByTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            $this->deleteByToken($token);
        }
    }

    /**
     *  Delete model media by id's
     *
     * @param string $mediable
     * @param iterable $ids
     *
     * @return boolean
     */
    public function purgeByMediableIds(string $mediable, iterable $ids) : bool
    {
        if (count($ids) === 0) {
            return false;
        }

        $this->scopeQuery(function ($query) use ($ids, $mediable) {
            return $query->whereIn('id', fn ($query) => $query->select('media_id')
                ->from(config('app.mediables_table'))
                ->where('mediable_type', $mediable)
                ->whereIn('mediable_id', $ids));
        })->get()->each->delete();

        $this->resetScope();

        return true;
    }
}
