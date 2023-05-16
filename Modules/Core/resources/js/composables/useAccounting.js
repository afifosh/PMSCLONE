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
import { formatMoney, formatNumber, toFixed } from 'accounting-js'

export function useAccounting(value) {
  return {
    toFixed,
    formatNumber: function (value) {
      return formatNumber(value, {
        precision: Innoclapps.config('currency.precision'),
        thousand: Innoclapps.config('currency.thousands_separator'),
        decimal: Innoclapps.config('currency.decimal_mark'),
      })
    },
    formatMoney: function (value) {
      return formatMoney(value, {
        symbol: Innoclapps.config('currency.symbol'),
        precision: Innoclapps.config('currency.precision'),
        thousand: Innoclapps.config('currency.thousands_separator'),
        decimal: Innoclapps.config('currency.decimal_mark'),
        format:
          Innoclapps.config('currency.symbol_first') == true ? '%s%v' : '%v%s',
      })
    },
  }
}
