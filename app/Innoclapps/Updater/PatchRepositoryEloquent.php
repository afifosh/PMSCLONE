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

namespace App\Innoclapps\Updater;

use App\Innoclapps\Models\Patch;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Contracts\Repositories\PatchRepository;

class PatchRepositoryEloquent extends AppRepository implements PatchRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Patch::class;
    }

    /**
     * Find patch by token
     *
     * @param string $token
     *
     * @return \App\Innoclapps\Models\Patch|null
     */
    public function findByToken(string $token) : ?Patch
    {
        return $this->findByField('token', $token)->first();
    }

    /**
     * Check whether the given patch token is applied
     *
     * @param string $token
     *
     * @return boolean
     */
    public function isApplied(string $token) : bool
    {
        return ! is_null($this->findByToken($token));
    }
}
