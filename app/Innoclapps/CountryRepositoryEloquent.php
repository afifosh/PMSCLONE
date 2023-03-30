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

use App\Innoclapps\Models\Country;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Contracts\Repositories\CountryRepository;

class CountryRepositoryEloquent extends AppRepository implements CountryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Country::class;
    }
}
