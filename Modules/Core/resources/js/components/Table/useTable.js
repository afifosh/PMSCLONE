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
import { useStore } from 'vuex'

export function useTable() {
  const store = useStore()

  function reloadTable(tableId) {
    Innoclapps.$emit('reload-resource-table', tableId)
  }

  function customizeTable(tableId, value = true) {
    store.commit('table/SET_CUSTOMIZE_VISIBILTY', {
      id: tableId,
      value: value,
    })
  }

  return { reloadTable, customizeTable }
}
