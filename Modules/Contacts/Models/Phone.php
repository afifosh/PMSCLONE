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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Contacts\Database\Factories\PhoneFactory;
use Modules\Contacts\Enums\PhoneType;
use Modules\Core\Contracts\Fields\TracksMorphManyModelAttributes;
use Modules\Core\CountryCallingCode;
use Modules\Core\Models\Model;

class Phone extends Model implements TracksMorphManyModelAttributes
{
    use HasFactory;

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['phoneable'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => PhoneType::class,
    ];

    /**
     * Get the phoneables
     */
    public function phoneable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the attributes the changes should be tracked on
     */
    public function trackAttributes(): string
    {
        return 'number';
    }

    /**
     * Generate random phone number
     */
    public static function generateRandomPhoneNumber(): string
    {
        return CountryCallingCode::random().mt_rand(100, 1000).'-'.mt_rand(100, 1000).'-'.mt_rand(100, 1000);
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            // For table serialization, will show the string value on the front-end
            'type' => $this->type->name,
        ]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): PhoneFactory
    {
        return PhoneFactory::new();
    }
}
