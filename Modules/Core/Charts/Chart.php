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

namespace Modules\Core\Charts;

use Illuminate\Http\Request;
use Modules\Core\Card\Card;

abstract class Chart extends Card
{
    /**
     * Indicates whether the chart values are amount
     */
    protected bool $amountValue = false;

    /**
     * Chart color/variant class
     */
    protected ?string $color = null;

    /**
     * The method to perform the line chart calculations
     *
     * @return mixed
     */
    abstract public function calculate(Request $request);

    /**
     * The chart available labels
     */
    public function labels($result): array
    {
        return [];
    }

    /**
     * Set chart color
     */
    public function color(string $color): static
    {
        $this->color = 'chart-'.$color;

        return $this;
    }

    /**
     * Prepate the data for the front-end
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'result' => $this->calculate(request()),
            'amount_value' => $this->amountValue,
            'color' => $this->color,
        ]);
    }
}
