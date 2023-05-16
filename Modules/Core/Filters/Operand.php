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

use Exception;
use JsonSerializable;
use Modules\Core\Fields\ChangesKeys;
use Modules\Core\Makeable;

class Operand implements JsonSerializable
{
    use Makeable, ChangesKeys;

    /**
     * @var \Modules\Core\Filters\Filter
     */
    public $rule;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var string
     */
    public $label;

    /**
     * Initialize Operand class
     *
     * @param  mixed  $value
     * @param  string  $label
     */
    public function __construct($value, $label)
    {
        $this->value = $value;
        $this->label = $label;
    }

    /**
     * Set the operand filter
     *
     * @param  \Modules\Core\Filters\Fitler|string  $rule
     * @return \Modules\Core\Filters\Operand
     */
    public function filter($rule)
    {
        if (is_string($rule)) {
            $rule = $rule::make($this->value);
        }

        if ($rule instanceof HasMany) {
            throw new Exception('Cannot use HasMany filter in operands');
        }

        $this->rule = $rule;

        return $this;
    }

    /**
     * jsonSerialize
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
            'valueKey' => $this->valueKey,
            'labelKey' => $this->labelKey,
            'rule' => $this->rule,
        ];
    }
}
