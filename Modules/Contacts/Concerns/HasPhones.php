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

namespace Modules\Contacts\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Contacts\Enums\PhoneType;
use Modules\Contacts\Models\Phone;

/** @mixin \Modules\Core\Models\Model */
trait HasPhones
{
    /**
     * Boot the HasPhones trait
     */
    protected static function bootHasPhones(): void
    {
        static::deleting(function ($model) {
            if (! $model->usesSoftDeletes() || $model->isForceDeleting()) {
                $model->phones()->delete();
            }
        });
    }

    /**
     * A model has phone number
     */
    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable')->orderBy('phones.created_at');
    }

    /**
     * Scope a query to include records by phone.
     */
    public function scopeByPhone(Builder $query, string $phone, ?PhoneType $type = null): void
    {
        $query->whereHas('phones', function ($query) use ($phone, $type) {
            if ($type) {
                $query->where('type', $type);
            }

            return $query->where('number', $phone);
        });
    }

    /**
     * Handle the attributes updated event
     */
    public function morphManyAtributesUpdated(string $relationName, array $new, array $old): void
    {
        if ($relationName === 'phones') {
            $valueProvider = function ($changed) {
                return collect($changed)->pluck('number')->filter()->implode(', ');
            };

            static::logDirtyAttributesOnLatestLog([
                'attributes' => ['phone' => $valueProvider($new)],
                'old' => ['phone' => $valueProvider($old)],
            ], $this);
        }
    }
}
