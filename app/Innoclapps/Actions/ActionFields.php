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

namespace App\Innoclapps\Actions;

class ActionFields
{
    /**
     * Create new instance of action request fields
     *
     * @param array $fields
     */
    public function __construct(protected array $fields)
    {
    }

    /**
     * Get field
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }
}
