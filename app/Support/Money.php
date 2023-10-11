<?php

namespace App\Support;

use Akaunting\Money\Money as BaseMoney;

class Money extends BaseMoney
{
  public function round(int|float $amount, int $mode = self::ROUND_HALF_DOWN): float
  {
    // $this->assertRoundingMode($mode);

    return round($amount, $this->currency->getPrecision(), config('app.rounding_mode'));
  }
}
