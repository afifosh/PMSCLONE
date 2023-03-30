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

use App\Innoclapps\Facades\Cards;

class Dashboard extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'cards'      => 'array',
        'is_default' => 'boolean',
        'user_id'    => 'int',
    ];

    /**
     * Get the default available dashboard cards
     *
     * @param \App\Models\User|null $user
     *
     * @return \Illuminate\Support\Collection
     */
    public static function defaultCards($user = null)
    {
        return Cards::registered()->filter->authorizedToSee($user)
            ->reject(fn ($card) => $card->onlyOnIndex === true)
            ->values()
            ->map(function ($card, $index) {
                return ['key' => $card->uriKey(), 'order' => $index + 1];
            });
    }
}
