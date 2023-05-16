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
import { ref, unref, watchEffect } from 'vue'

export function useResource(resourceName) {
  const singularName = ref(null)

  function getSingularName(name) {
    return Innoclapps.config(`resources.${unref(name || resourceName)}`)
      .singularName
  }

  watchEffect(() => {
    if (unref(resourceName)) {
      singularName.value = getSingularName()
    } else {
      singularName.value = null
    }
  })

  return { singularName, getSingularName }
}
