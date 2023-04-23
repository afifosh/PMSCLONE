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

namespace App\Innoclapps\Timeline;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class Timelineables
{
    /**
     * @var \Illuminate\Support\Collection|null
     */
    protected static $models;

    /**
     * Discover and register the timelineables
     *
     * @return void
     */
    public static function discover()
    {
        $instance      = new static;
        $timelineables = $instance->getTimelineables()->all();

        foreach ($instance->getSubjects() as $subject) {
            static::register($timelineables, $subject);
        }
    }

    /**
     * Register the given timelineables
     *
     * @param string|array $timelineables
     * @param string $subject
     *
     * @return void
     */
    public static function register($timelineables, $subject)
    {
        Timeline::acceptsPinsFrom([
                'subject' => $subject,
                'as'      => $subject::getTimelineSubjectKey(),
                'accepts' => array_map(function ($class) {
                    return ['as' => $class::timelineKey(), 'timelineable_type' => $class];
                }, Arr::wrap($timelineables)),
            ]);
    }

    /**
     * Get the timelineables
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTimelineables()
    {
        return (new static)->getModels()
            ->filter(fn ($model) => static::isTimelineable($model))
            ->values();
    }

    /**
     * Check whether the given model is timelineable
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return boolean
     */
    public static function isTimelineable($model)
    {
        return in_array(Timelineable::class, class_uses_recursive($model));
    }

    /**
     * Check whether the given model has timeline
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return boolean
     */
    public static function hasTimeline($model)
    {
        return in_array(HasTimeline::class, class_uses_recursive($model));
    }

    /**
     * Get the subjects
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSubjects()
    {
        return (new static)->getModels()->filter(function ($model) {
            return in_array(HasTimeline::class, class_uses_recursive($model));
        })->values();
    }

    /**
     * Get the application models
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getModels()
    {
        if (static::$models) {
            return static::$models;
        }

        return static::$models = collect((new Finder)->in([
            app_path('Models'),
            app_path('Innoclapps/Models'),
        ])->files()->name('*.php'))
            ->map(function ($model) {
                $namespace = app()->getNamespace();

                return $namespace . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($model->getRealPath(), realpath(app_path()) . DIRECTORY_SEPARATOR)
                );
            });
    }
}
