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
import { reactive } from 'vue'
import Form from '~/Core/resources/js/services/Form/Form'

export function useForm(data = {}, options = {}) {
  const form = reactive(new Form(data, options))

  return { form }
}
