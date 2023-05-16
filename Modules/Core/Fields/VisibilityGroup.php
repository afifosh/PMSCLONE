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

namespace Modules\Core\Fields;

use Modules\Core\Table\Column;

class VisibilityGroup extends Field
{
    /**
     * Field component
     */
    public ?string $component = 'visibility-group-field';

    /**
     * Initialize new VisibilityGroup instance class
     */
    public function __construct()
    {
        parent::__construct(...func_get_args());

        $this->rules(['nullable', 'array'])
            ->excludeFromZapierResponse()
            ->strictlyForForms()
            ->excludeFromDetail()
            ->excludeFromImport()
            ->excludeFromImportSample()
            ->excludeFromSettings();
    }

    /**
     * Provides the relationships that should be eager loaded when quering resource data
     */
    public function withRelationships(): array
    {
        return ['visibilityGroup.teams', 'visibilityGroup.users'];
    }

    /**
     * Resolve the field value for JSON Resource
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array|null
     */
    public function resolveForJsonResource($model)
    {
        if (! $model->relationLoaded('visibilityGroup')) {
            return null;
        }

        return [$this->attribute => $model->visibilityGroupData()];
    }

    /**
     * Get the mailable template placeholder
     *
     * @param  \Modules\Core\Models\Model  $model
     * @return null
     */
    public function mailableTemplatePlaceholder($model)
    {
        return null;
    }

    /**
     * Provide the column used for index
     *
     * @return null
     */
    public function indexColumn(): ?Column
    {
        return null;
    }

    /**
     * Resolve the displayable field value
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return null
     */
    public function resolveForDisplay($model)
    {
        return null;
    }

    /**
     * Resolve the field value for export
     *
     * @param  \Modules\Core\Models\Model  $model
     * @return null
     */
    public function resolveForExport($model)
    {
        return null;
    }

    /**
     * Resolve the field value for import
     *
     * @param  string|null  $value
     * @param  array  $row
     * @param  array  $original
     * @return null
     */
    public function resolveForImport($value, $row, $original)
    {
        return null;
    }
}
