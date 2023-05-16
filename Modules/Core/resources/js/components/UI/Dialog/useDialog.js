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
import { onMounted, unref } from 'vue'
import { useGlobalEventListener } from '~/Core/resources/js/composables/useGlobalEventListener'

export function useDialog(show, hide, dialogId) {
  function globalShow(id) {
    if (id === unref(dialogId)) {
      show()
    }
  }

  function globalHide(id) {
    if (id === unref(dialogId)) {
      hide()
    }
  }

  onMounted(() => {
    useGlobalEventListener('modal-hide', globalHide)
    useGlobalEventListener('modal-show', globalShow)
  })
}
