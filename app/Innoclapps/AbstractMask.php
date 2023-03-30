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

namespace App\Innoclapps;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractMask implements JsonSerializable, Arrayable
{
    /**
     * Initialize the mask
     *
     * @param array|object $entity
     */
    public function __construct(protected $entity)
    {
    }

    /**
     * Get the entity
     *
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
