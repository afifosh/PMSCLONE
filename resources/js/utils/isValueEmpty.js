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
function isValueEmpty(value) {
  // Perform checks for all data types
  // https://javascript.info/types
  if (value !== null && typeof value !== 'undefined') {
    if (typeof value === 'string' && value !== '') {
      return false
    } else if (typeof value === 'array' && value.length > 0) {
      return false
    } else if (typeof value === 'object' && Object.keys(value).length > 0) {
      return false
    } else if (typeof value === 'boolean' || typeof value === 'number') {
      return false
    }
  }

  return true
}

export default isValueEmpty
