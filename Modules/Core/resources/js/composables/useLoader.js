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
import { ref } from 'vue'

export function useLoader(defaultValue = false) {
  const isLoading = ref(defaultValue)

  function setLoading(value = true) {
    isLoading.value = value
  }

  return { setLoading, isLoading }
}
