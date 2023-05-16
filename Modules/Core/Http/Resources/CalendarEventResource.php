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

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Modules\Core\Contracts\DisplaysOnCalendar */
class CalendarEventResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'title' => $this->getCalendarTitle($request->viewName),
            'start' => $this->getCalendarStartDate($request->viewName),
            'end' => $this->getCalendarEndDate($request->viewName),
            'allDay' => $this->isAllDay(),
            'isReadOnly' => $request->user()->cant('update', $this->resource),
            'textColor' => method_exists($this->resource, 'getCalendarEventTextColor') ?
                $this->getCalendarEventTextColor() :
                null,
            'backgroundColor' => $bgColor = method_exists($this->resource, 'getCalendarEventBgColor') ?
                $this->getCalendarEventBgColor() :
                '',
            'borderColor' => method_exists($this->resource, 'getCalendarEventBorderColor') ?
                $this->getCalendarEventBorderColor() :
                $bgColor,
            'extendedProps' => [
                'event_type' => strtolower(class_basename($this->resource)),
            ],
        ];
    }
}
