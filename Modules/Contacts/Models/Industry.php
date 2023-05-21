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
use Modules\Contacts\Database\Factories\IndustryFactory;
use Modules\Core\Models\Model;

class Industry extends Model
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
            if ($model->companies()->count() > 0) {
                abort(409, __(
                    'core::resource.associated_delete_warning',
                    [
                        'resource' => __('contacts::company.industry.industry'),
                    ]
                ));
            }
            $model->companies()->onlyTrashed()->update(['industry_id' => null]);
        });
    }

    /**
     * An industry has many companies
     */
    public function companies(): HasMany
    {
        return $this->hasMany(\Modules\Contacts\Models\Company::class);
    }

    /**
     * Name attribute accessor
     *
     * Supports translation from language file
     */
    public function name(): Attribute
    {
        return Attribute::get(function (string $value, array $attributes) {
            $customKey = 'custom.industry.'.$attributes['id'];

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
    protected static function newFactory(): IndustryFactory
    {
        return IndustryFactory::new();
    }
}
