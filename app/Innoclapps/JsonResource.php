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

namespace App\Innoclapps;

use App\Innoclapps\Contracts\Presentable;
use App\Innoclapps\Contracts\Primaryable;
use App\Innoclapps\Timeline\Timelineables;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;

class JsonResource extends BaseJsonResource
{
    use ProvidesModelAuthorizations;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected static $topLevelResource;

    /**
     * Set the top level resource
     *
     * @param \Illuminate\Database\Eloquent\Model $resource
     *
     * @return void
     */
    public static function topLevelResource($resource)
    {
        static::$topLevelResource = $resource;
    }

    /**
     * Provide common data for the resource
     *
     * @param array $data
     * @param \App\Innoclapps\Resources\Http\ResourceRequest $request
     *
     * @return array
     */
    protected function withCommonData($data, $request)
    {
        array_unshift($data, $this->merge([
            'id' => $this->getKey(),
        ]));

        $data[] = $this->mergeWhen($this->resource instanceof Presentable, [
            'display_name' => $this->display_name,
            'path'         => $this->path,
        ]);

        $data[] = $this->mergeWhen($this->resource instanceof Primaryable, function () {
            return   [
                'is_primary' => $this->isPrimary(),
            ];
        });

        $data[] = $this->mergeWhen($this->usesTimestamps(), function () {
            return [
                $this->getCreatedAtColumn() => $this->{$this->getCreatedAtColumn()},
                $this->getUpdatedAtColumn() => $this->{$this->getUpdatedAtColumn()},
            ];
        });

        if (! $request->isZapier()) {
            if (Timelineables::isTimelineable($this->resource)) {
                $data[] = $this->merge([
                    'timeline_component' => $this->getTimelineComponent(),
                    'timeline_relation'  => $this->getTimelineRelation(),
                    'timeline_key'       => $this->timelineKey(),
                ]);

                if (static::$topLevelResource &&
                        $this->relationLoaded('pinnedTimelineSubjects')) {
                    $pinnedSubject = $this->getPinnedSubject(static::$topLevelResource::class, static::$topLevelResource->getKey());

                    $data[] = $this->merge([
                        'is_pinned'   => ! is_null($pinnedSubject),
                        'pinned_date' => $pinnedSubject?->created_at,
                    ]);
                }
            }

            $data[] = $this->mergeWhen(Timelineables::hasTimeline($this->resource), function () {
                return [
                    'timeline_subject_key' => $this->getTimelineSubjectKey(),
                ];
            });

            $data[] = $this->mergeWhen($authorizations = $this->getAuthorizations($this->resource), [
                'authorizations' => $authorizations,
            ]);

            $data[] = $this->merge([
                'was_recently_created' => $this->resource->wasRecentlyCreated,
            ]);
        }

        return $data;
    }
}
