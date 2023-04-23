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

namespace App\Innoclapps\Resources\Http;

class UpdateResourceRequest extends ResourcefulRequest
{
    /**
     * Get the fields for the current request
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    public function fields()
    {
        $this->resource()->setModel($this->record());

        return $this->resource()->resolveUpdateFields();
    }
}
