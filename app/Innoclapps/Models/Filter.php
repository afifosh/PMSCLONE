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

namespace App\Innoclapps\Models;

use App\Innoclapps\Concerns\HasMeta;
use Illuminate\Support\Facades\Lang;
use App\Innoclapps\Contracts\Metable;
use Database\Factories\FilterFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Filter extends Model implements Metable
{
    use HasMeta, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'identifier', 'rules', 'is_shared', 'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'rules'       => 'array',
        'is_shared'   => 'boolean',
        'is_readonly' => 'boolean',
        'user_id'     => 'int',
    ];

    /**
     * Get the filter owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Admin::class);
    }

    /**
     * Filter has many default views
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function defaults()
    {
        return $this->hasMany(FilterDefaultView::class);
    }

    /**
     * Indicates whether the filter is system default
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function isSystemDefault() : Attribute
    {
        return Attribute::get(fn () => is_null($this->user_id));
    }

    /**
     * Name attribute accessor
     *
     * Supports translation from language file
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function name() : Attribute
    {
        return Attribute::get(function ($value) {
            $customKey = 'custom.filter.' . $value;
            if (Lang::has($customKey)) {
                return __($customKey);
            } elseif (Lang::has($value)) {
                return __($value);
            }

            return $value;
        });
    }

    /**
     * Set rules attribute mutator
     *
     * We will check if the passed value is array and there are
     * children defined in the array, if not, we will assume the the
     * children is passed as one big array
     *
     * @param \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function rules() : Attribute
    {
        return Attribute::set(function ($value) {
            if (is_array($value) && ! array_key_exists('children', $value)) {
                $value = [
                    'condition' => 'and',
                    'children'  => $value,
                ];
            }

            return json_encode(is_array($value) ? $value : []);
        });
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new FilterFactory;
    }
}
