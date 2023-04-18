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

namespace App\Innoclapps\Models;

use Illuminate\Support\Str;
use App\Innoclapps\Concerns\HasMeta;
use App\Innoclapps\Contracts\Metable;
use Plank\Mediable\Media as BaseMedia;
use Illuminate\Support\Facades\Storage;

class Media extends BaseMedia implements Metable
{
    use HasMeta;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * Boot the model
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        /**
         * When creating new media, we will add random key identifier
         */
        static::creating(function ($model) {
            $model->token = Str::uuid()->toString();

            return $model;
        });

        /**
         * On media deleted, remove the created folder for the resource
         */
        static::deleted(function ($model) {
            tap(Storage::disk($model->disk), function ($disk) use ($model) {
                $files = $disk->files($model->directory);

                if (count($files) === 0) {
                    $disk->deleteDirectory($model->directory);
                }
            });
        });
    }

    /**
     * A media may be pending
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pendingData()
    {
        return $this->belongsTo(PendingMedia::class, 'id', 'media_id');
    }

    /**
     * Check whether the media video is HTML5 supported video
     *
     * @see https://www.w3schools.com/html/html5_video.asp
     *
     * @return boolean
     */
    public function isHtml5SupportedVideo()
    {
        return in_array($this->extension, ['mp4', 'webm', 'ogg']);
    }

    /**
     * Check whether the media audio is HTML5 supported audio
     *
     * @see https://www.w3schools.com/html/html5_audio.asp
     *
     * @return boolean
     */
    public function isHtml5SupportedAudio()
    {
        return in_array($this->extension, ['mp3', 'wav', 'ogg']);
    }

    /**
     * Get the media item view URL
     *
     * @return string
     */
    public function getViewUrl()
    {
        return url("/media/{$this->token}");
    }

    /**
     * Get the media item preview URL
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return url($this->previewPath());
    }

    /**
     * Get the media item preview URL
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return url($this->downloadPath());
    }

    /**
     * Get the media item download URI
     *
     * @return string
     */
    public function downloadPath()
    {
        return "/media/{$this->token}/download";
    }

    /**
     * Get the media item preview URI
     *
     * @return string
     */
    public function previewPath()
    {
        return "/media/{$this->token}/preview";
    }
}
