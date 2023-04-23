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

namespace App\Resources\Contact;

use App\Contracts\Repositories\ContactRepository;
use App\Innoclapps\Resources\ResourcefulHandlerWithFields;

class ResourcefulHandler extends ResourcefulHandlerWithFields
{
    /**
     * Handle the resource store action
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store()
    {
        $model = parent::store();

        if ($model->email && (bool) settings('auto_associate_company_to_contact')) {
            app(ContactRepository::class)->associateCompaniesByEmailDomain($model);
        }

        return $model;
    }
}
