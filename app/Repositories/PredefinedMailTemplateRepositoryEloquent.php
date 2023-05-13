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

namespace App\Repositories;

use App\Models\PredefinedMailTemplate;
use App\Innoclapps\Repository\AppRepository;
use App\Contracts\Repositories\PredefinedMailTemplateRepository;

class PredefinedMailTemplateRepositoryEloquent extends AppRepository implements PredefinedMailTemplateRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return PredefinedMailTemplate::class;
    }
}
