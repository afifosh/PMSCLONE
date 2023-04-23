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

namespace App\Innoclapps\Workflow;

use App\Innoclapps\Models\Workflow;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Contracts\Repositories\WorkflowRepository;

class WorkflowRepositoryEloquent extends AppRepository implements WorkflowRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Workflow::class;
    }
}
