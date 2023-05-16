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

namespace Modules\Activities\Resource;

use Illuminate\Http\Request;
use Modules\Activities\Http\Resources\ActivityTypeResource;
use Modules\Core\Contracts\Resources\Resourceful;
use Modules\Core\Fields\ColorSwatches;
use Modules\Core\Fields\IconPicker;
use Modules\Core\Fields\Text;
use Modules\Core\Resource\Resource;

class ActivityType extends Resource implements Resourceful
{
    /**
     * The column the records should be default ordered by when retrieving
     */
    public static string $orderBy = 'name';

    /**
     * The model the resource is related to
     */
    public static string $model = 'Modules\Activities\Models\ActivityType';

    /**
     * Get the json resource that should be used for json response
     */
    public function jsonResource(): string
    {
        return ActivityTypeResource::class;
    }

    /**
     * Set the available resource fields
     */
    public function fields(Request $request): array
    {
        return [
            Text::make('name', __('activities::activity.type.name'))->rules('required', 'string', 'max:191')->unique(static::$model),
            IconPicker::make('icon', __('activities::activity.type.icon'))->rules('required', 'string', 'max:50')->unique(static::$model),
            ColorSwatches::make('swatch_color', __('core::app.colors.color'))->rules('nullable', 'string', 'max:7'),
        ];
    }

    /**
     * Get the displayable singular label of the resource
     */
    public static function singularLabel(): string
    {
        return __('activities::activity.type.type');
    }

    /**
     * Get the displayable label of the resource
     */
    public static function label(): string
    {
        return __('activities::activity.type.types');
    }
}
