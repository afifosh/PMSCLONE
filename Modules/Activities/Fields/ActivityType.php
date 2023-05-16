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

namespace Modules\Activities\Fields;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Activities\Http\Resources\ActivityTypeResource;
use Modules\Activities\Models\ActivityType as ActivityTypeModel;
use Modules\Activities\Models\ActivityType as Model;
use Modules\Core\Fields\BelongsTo;

class ActivityType extends BelongsTo
{
    /**
     * Field component
     */
    public ?string $component = 'activity-type-field';

    /**
     * Create new instance of ActivityType field
     */
    public function __construct()
    {
        parent::__construct('type', ActivityTypeModel::class, __('activities::activity.type.type'));

        $this->withDefaultValue(function () {
            if (is_null($type = Model::getDefaultType())) {
                return null;
            }

            try {
                return ActivityTypeModel::select('id')->findOrFail($type)->getKey();
            } catch (ModelNotFoundException) {
            }
        })
            ->setJsonResource(ActivityTypeResource::class)
            ->tapIndexColumn(function ($column) {
                $column->label(__('activities::activity.type.type'))
                    ->select(['icon', 'swatch_color'])
                    ->primary(false)
                    ->width('130px')
                    ->minWidth('130px')
                    ->useComponent('table-data-option-column');
            })
            ->acceptLabelAsValue(false);
    }
}
