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

namespace App\Innoclapps\Filters;

use App\Innoclapps\Fields\HasOptions;
use App\Innoclapps\Fields\ChangesKeys;
use App\Innoclapps\Facades\Timezone as Facade;

class Timezone extends Filter
{
    use HasOptions,
        ChangesKeys;

    /**
     * @param string $field
     * @param string|null $label
     * @param null|array $operators
     */
    public function __construct($field, $label = null, $operators = null)
    {
        parent::__construct($field, $label, $operators);

        $this->options(collect(Facade::toArray())->mapWithKeys(function ($timezone) {
            return [$timezone => $timezone];
        })->all());
    }

    /**
     * Defines a filter type
     *
     * @return string
     */
    public function type() : string
    {
        return 'select';
    }
}
