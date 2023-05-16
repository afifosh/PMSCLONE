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
export function getDefaultQuery() {
  return {
    condition: 'and',
    children: [],
  }
}

export function isNullableOperator(operator) {
  return (
    ['is_empty', 'is_not_empty', 'is_null', 'is_not_null'].indexOf(operator) >=
    0
  )
}

export function isBetweenOperator(operator) {
  return ['between', 'not_between'].indexOf(operator) >= 0
}

export function needsArray(operator) {
  return ['in', 'not_in', 'between', 'not_between'].includes(operator)
}
