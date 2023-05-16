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

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Core\Concerns\HasMeta;
use Modules\Core\Contracts\Metable;
use Plank\Mediable\Media as BaseMedia;

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
     * Mark the current media instance as pending media.
     */
    public function markAsPending(string $draftId): PendingMedia
    {
        $pendingModel = new PendingMedia(['media_id' => $this->id, 'draft_id' => $draftId]);

        $pendingModel->save();

        return $pendingModel;
    }

    /**
     * A media may be pending
     */
    public function pendingData(): BelongsTo
    {
        return $this->belongsTo(PendingMedia::class, 'id', 'media_id');
    }

    /**
     * Check whether the media video is HTML5 supported video
     *
     * @see https://www.w3schools.com/html/html5_video.asp
     */
    public function isHtml5SupportedVideo(): bool
    {
        return in_array($this->extension, ['mp4', 'webm', 'ogg']);
    }

    /**
     * Check whether the media audio is HTML5 supported audio
     *
     * @see https://www.w3schools.com/html/html5_audio.asp
     */
    public function isHtml5SupportedAudio(): bool
    {
        return in_array($this->extension, ['mp3', 'wav', 'ogg']);
    }

    /**
     * Get the media item view URL
     */
    public function getViewUrl(): string
    {
        return url("/media/{$this->token}");
    }

    /**
     * Get the media item preview URL
     */
    public function getPreviewUrl(): string
    {
        return url($this->previewPath());
    }

    /**
     * Get the media item preview URL
     */
    public function getDownloadUrl(): string
    {
        return url($this->downloadPath());
    }

    /**
     * Get the media item download URI
     */
    public function downloadPath(): string
    {
        return "/media/{$this->token}/download";
    }

    /**
     * Get the media item preview URI
     */
    public function previewPath(): string
    {
        return "/media/{$this->token}/preview";
    }

    /**
     * Scope a query to only include media by given token.
     */
    public function scopeByToken(Builder $query, string $token): void
    {
        $query->where('token', $token);
    }

    /**
     *  Delete model media by id's
     */
    public function purgeByMediableIds(string $mediable, iterable $ids): bool
    {
        if (count($ids) === 0) {
            return false;
        }

        $this->whereIn('id', fn ($query) => $query->select('media_id')
            ->from(config('mediable.mediables_table'))
            ->where('mediable_type', $mediable)
            ->whereIn('mediable_id', $ids))
            ->get()->each->delete();

        return true;
    }
}
