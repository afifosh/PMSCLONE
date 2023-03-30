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

namespace App\Innoclapps\Charts;

use Closure;
use JsonSerializable;

class ChartResult implements JsonSerializable
{
    /**
     * Chart colors
     *
     * @var array
     */
    protected $colors = [];

    /**
     * Create a new partition result instance.
     *
     * @param array $value
     *
     * @return void
     */
    public function __construct(public array $value)
    {
    }

    /**
     * Format the labels for the partition result.
     *
     * @param \Closure $callback
     *
     * @return static
     */
    public function label(Closure $callback) : static
    {
        $this->value = collect($this->value)->mapWithKeys(function ($value, $label) use ($callback) {
            return [$callback($label) => $value];
        })->all();

        return $this;
    }

    /**
     * Set the chart colors
     *
     * @param array $colors
     *
     * @return static
     */
    public function colors(array $colors) : static
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * Prepare the metric result for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return collect($this->value)->map(function ($value, $label) {
            return array_filter([
                    'label' => $label,
                    'value' => $value,
                    'color' => data_get($this->colors, $label),
                ], function ($value) {
                    return ! is_null($value);
                });
        })->values()->all();
    }
}
