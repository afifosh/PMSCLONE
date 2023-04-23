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

namespace App\Innoclapps\Resources\Import;

use App\Innoclapps\Models\Import;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Contracts\Repositories\ImportRepository;

class ImportRepositoryEloquent extends AppRepository implements ImportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Import::class;
    }

    /**
     * Persist the model in storage
     *
     * @param array $attributes
     *
     * @return \App\Innoclapps\Models\Import
     */
    public function create(array $attributes)
    {
        $import = parent::create($attributes);

        $import->loadMissing('user');

        return $import;
    }
}
