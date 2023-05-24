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

function isISODate(str) {
  // First perform the checks below, less IQ
  if (!isString(str)) {
    return false
  }

  if (str.indexOf('-') === 1) {
    return false
  }

  // 2020-04-02T03:39:56.000000Z
  return /\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3,6}Z/.test(str)
}

export default isISODate
