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
import { useApp } from '~/Core/resources/js/composables/useApp'

export function useSignature() {
  const { currentUser } = useApp()

  function addSignature(message = '') {
    return (
      message +
      (currentUser.value.mail_signature
        ? '<br /><br />----------<br />' + currentUser.value.mail_signature
        : '')
    )
  }

  return {
    addSignature,
  }
}
