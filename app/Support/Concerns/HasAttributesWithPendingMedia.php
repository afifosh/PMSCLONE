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

namespace App\Support\Concerns;

use App\Innoclapps\EditorPendingMediaProcessor;

trait HasAttributesWithPendingMedia
{
    /**
     * Boot HasAttributesWithPendingMedia trait
     *
     * @return void
     */
    protected static function bootHasAttributesWithPendingMedia()
    {
        static::updated(function ($model) {
            static::runMediaProcessor($model);
        });

        static::created(function ($model) {
            static::runMediaProcessor($model);
        });

        static::deleted(function ($model) {
            if (! $model->usesSoftDeletes() || $model->isForceDeleting()) {
                static::createMediaProcessor()->deleteAllViaModel(
                    $model,
                    $model->attributesWithPendingMedia()
                );
            }
        });
    }

    /**
     * Get the attributes that may contain pending media
     *
     * @return array|string
     */
    abstract public function attributesWithPendingMedia() : array|string;

    /**
     * Run the editor media processor
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    protected static function runMediaProcessor($model)
    {
        static::createMediaProcessor()->processViaModel(
            $model,
            $model->attributesWithPendingMedia()
        );
    }

    /**
     * Create media processor
     *
     * @return \App\Innoclapps\EditorPendingMediaProcessor
     */
    protected static function createMediaProcessor() : EditorPendingMediaProcessor
    {
        return new EditorPendingMediaProcessor();
    }
}
