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

use Illuminate\Support\Str;
use Plank\Mediable\Mediable;

trait HasMedia
{
    use Mediable;

    /**
     * Boot HasMedia trait
     *
     * @return void
     */
    protected static function bootHasMedia()
    {
        static::deleting(function ($model) {
            if (! $model->usesSoftDeletes() || $model->isForceDeleting()) {
                $model->purgeMedia();
            }
        });
    }

    /**
     * Purge the model media
     *
     * The function deletes the files and the storage folder when empty
     *
     * @return void
     */
    public function purgeMedia() : void
    {
        $this->media()->get()->each->delete();
    }

    /**
     * Get the model media directory
     *
     * @return string
     */
    public function getMediaDirectory() : string
    {
        $folder = Str::kebab(class_basename(get_called_class()));

        return config('innoclapps.media.directory') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $this->id;
    }

    /**
     * Get the model media tag
     *
     * @return array
     */
    public function getMediaTags() : array
    {
        return ['profile'];
    }
}
