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
import isString from 'lodash/isString'

function isStandardDateTime(str) {
  // First perform the checks below, less IQ
  if (!isString(str)) {
    return false
  }

  if (
    str.indexOf('-') <= 1 ||
    str.indexOf(' ') === 0 ||
    str.indexOf(':') === 0
  ) {
    return false
  }

  return /\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(str)
}

export default isStandardDateTime
