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

namespace Modules\Activities\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Lang;
use Modules\Activities\Database\Factories\ActivityTypeFactory;
use Modules\Core\Contracts\Primaryable;
use Modules\Core\Models\Model;

class ActivityType extends Model implements Primaryable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'swatch_color', 'icon',
    ];

    /**
     * The fields for the model that are searchable.
     */
    protected static array $searchableFields = [
        'name' => 'like',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->isPrimary()) {
                abort(409, __('activities::activity.type.delete_primary_warning'));
            } elseif (static::getDefaultType() == $model->getKey()) {
                abort(409, __('activities::activity.type.delete_is_default'));
            } elseif ($model->calendars()->count() > 0) {
                abort(409, __('activities::activity.type.delete_usage_calendars_warning'));
            } elseif ($model->activities()->count() > 0) {
                abort(409, __('activities::activity.type.delete_usage_warning'));
            }

            // We must delete the trashed activities when the type is deleted
            // as we don't have any option to do with the activity except to associate
            // it with other type (if found) but won't be accurate.
            $model->activities()->onlyTrashed()->get()->each->forceDelete();
        });
    }

    /**
     * Get the calendars that the type is added as create event type
     */
    public function calendars(): HasMany
    {
        return $this->hasMany(\Modules\Activities\Models\Calendar::class);
    }

    /**
     * Set the activity type
     */
    public static function setDefault(int $id): void
    {
        settings(['default_activity_type' => $id]);
    }

    /**
     * Get the activity default type
     */
    public static function getDefaultType(): ?int
    {
        return settings('default_activity_type');
    }

    /**
     * A activity type has many activities
     */
    public function activities(): HasMany
    {
        return $this->hasMany(\Modules\Activities\Models\Activity::class);
    }

    /**
     * Check whether the activity type is primary
     */
    public function isPrimary(): bool
    {
        return ! is_null($this->flag);
    }

    /**
     * Find activity by flag.
     */
    public static function findByFlag(string $flag): ActivityType
    {
        return static::where('flag', $flag)->first();
    }

    /**
     * Name attribute accessor
     *
     * Supports translation from language file
     */
    public function name(): Attribute
    {
        return Attribute::get(function (string $value, array $attributes) {
            $customKey = 'custom.activity_type.'.$attributes['id'];

            if (Lang::has($customKey)) {
                return __($customKey);
            } elseif (Lang::has($value)) {
                return __($value);
            }

            return $value;
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ActivityTypeFactory
    {
        return ActivityTypeFactory::new();
    }
}
