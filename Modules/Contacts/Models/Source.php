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

namespace Modules\Contacts\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Lang;
use Modules\Contacts\Database\Factories\SourceFactory;
use Modules\Core\Contracts\Primaryable;
use Modules\Core\Models\Model;

class Source extends Model implements Primaryable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
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
                abort(409, __('contacts::source.delete_primary_warning'));
            }

            if ($model->contacts()->count() > 0 || $model->companies()->count() > 0) {
                abort(409, __(
                    'core::resource.associated_delete_warning',
                    [
                        'resource' => __('contacts::source.source'),
                    ]
                ));
            }

            $model->contacts()->onlyTrashed()->update(['source_id' => null]);
            $model->companies()->onlyTrashed()->update(['source_id' => null]);
        });
    }

    /**
     * A source has many contacts
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(\Modules\Contacts\Models\Contact::class);
    }

    /**
     * A source has many companies
     */
    public function companies(): HasMany
    {
        return $this->hasMany(\Modules\Contacts\Models\Company::class);
    }

    /**
     * Check whether the source is primary
     */
    public function isPrimary(): bool
    {
        return ! is_null($this->flag);
    }

    /**
     * Find source by flag.
     */
    public static function findByFlag(string $flag): Source
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
            $customKey = 'custom.source.'.$attributes['id'];

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
    protected static function newFactory(): SourceFactory
    {
        return SourceFactory::new();
    }
}
