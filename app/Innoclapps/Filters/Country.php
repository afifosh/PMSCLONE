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

namespace App\Innoclapps\Filters;

use App\Innoclapps\Contracts\Repositories\CountryRepository;

class Country extends Select
{
    /**
     * Initialize Country filter
     */
    public function __construct()
    {
        parent::__construct('country_id', __('fields.companies.country.name'));

        $this->valueKey('id')->labelKey('name')
            ->options(fn () => $this->countries());
    }

    /**
     * Get the filter countries
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function countries()
    {
        return resolve(CountryRepository::class)->get(['id', 'name'])->all();
    }
}
