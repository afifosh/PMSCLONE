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

namespace Modules\Contacts\Filters;

use Modules\Contacts\Models\Source as SoruceModel;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Filters\Select;

class SourceFilter extends Select
{
    /**
     * Initialize Source class
     */
    public function __construct()
    {
        parent::__construct('source_id', __('contacts::fields.companies.source.name'));

        $this->withNullOperators()
            ->valueKey('id')
            ->labelKey('name')
            ->options(Innoclapps::resourceByModel(SoruceModel::class));
    }
}
