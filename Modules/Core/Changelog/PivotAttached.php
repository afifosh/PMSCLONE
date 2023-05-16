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

namespace Modules\Core\Changelog;

class PivotAttached extends PivotLogger
{
    /**
     * Log pivot attached event
     *
     * Used e.q. when attaching company
     *
     * @param  \Modules\Core\Models\Model  $attachedTo The model where the pivot is attached
     * @param  array  $pivotIds Attached pivot IDs
     * @param  string  $relation The relation name the event occured
     * @return null
     */
    public static function log($attachedTo, $pivotIds, $relation)
    {
        $pivotModels = static::getRelatedPivotIds($attachedTo, $pivotIds, $relation);

        foreach ($pivotModels as $pivotModel) {
            static::perform($attachedTo, $pivotModel, $relation, 'attached');
        }
    }
}
