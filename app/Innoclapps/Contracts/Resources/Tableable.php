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

namespace App\Innoclapps\Contracts\Resources;

use Illuminate\Http\Request;
use App\Innoclapps\Table\Table;

interface Tableable
{
    /**
     * Provide the resource table class
     *
     * @param \App\Innoclapps\Repository\BaseRepository $repository
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Innoclapps\Table\Table
     */
    public function table($repository, Request $request) : Table;
}
