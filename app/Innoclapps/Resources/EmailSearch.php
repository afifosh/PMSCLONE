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

namespace App\Innoclapps\Resources;

class EmailSearch extends GlobalSearch
{
    /**
     * Provide the model data for the response
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \App\Innoclapps\Resources\resource $resource
     *
     * @return array
     */
    protected function data($model, $resource) : array
    {
        return [
            'id'           => $model->getKey(),
            'address'      => $model->email,
            'name'         => $model->display_name,
            'path'         => $model->path,
            'resourceName' => $resource->name(),
        ];
    }
}
