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

namespace App\Innoclapps\Fields;

use App\Innoclapps\EditorPendingMediaProcessor;

class Editor extends Field
{
    /**
     * Field component
     */
    public ?string $component = 'editor-field';

    /**
     * Handle the resource record "created" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordCreated($model)
    {
        $this->runImagesProcessor($model);
    }

    /**
     * Handle the resource record "updated" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordUpdated($model)
    {
        $this->runImagesProcessor($model);
    }

    /**
     * Handle the resource record "deleted" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordDeleted($model)
    {
        $this->createImagesProcessor()->deleteAllViaModel(
            $model,
            $this->attribute
        );
    }

    /**
     * Run the editor images processor
     *
     * @param $this $model
     *
     * @return void
     */
    protected function runImagesProcessor($model)
    {
        $this->createImagesProcessor()->processViaModel(
            $model,
            $this->attribute
        );
    }

    /**
     * Resolve the field value
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return string
     */
    public function resolve($model)
    {
        return clean(parent::resolve($model));
    }

    /**
     * Create editor images processor
     *
     * @return \App\Innoclapps\EditorPendingMediaProcessor
     */
    protected function createImagesProcessor()
    {
        return new EditorPendingMediaProcessor();
    }
}