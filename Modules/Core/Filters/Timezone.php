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

namespace Modules\Core\Filters;

use Modules\Core\Facades\Timezone as Facade;
use Modules\Core\Fields\ChangesKeys;
use Modules\Core\Fields\HasOptions;

class Timezone extends Filter
{
    use HasOptions,
        ChangesKeys;

    /**
     * @param  string  $field
     * @param  string|null  $label
     * @param  null|array  $operators
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
     */
    public function type(): string
    {
        return 'select';
    }
}
