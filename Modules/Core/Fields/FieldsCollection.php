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

namespace Modules\Core\Fields;

use Illuminate\Support\Collection;

class FieldsCollection extends Collection
{
    /**
     * Find field by attribute
     *
     * @param  string  $attribute
     * @return \Modules\Core\Fields\Field|null
     */
    public function find($attribute)
    {
        return $this->firstWhere('attribute', $attribute);
    }

    /**
     * Find field by request attribute
     *
     * @param  string  $attribute
     * @return \Modules\Core\Fields\Field|null
     */
    public function findByRequestAttribute($attribute)
    {
        return $this->first(function ($field) use ($attribute) {
            return $field->requestAttribute() === $attribute;
        });
    }
}
