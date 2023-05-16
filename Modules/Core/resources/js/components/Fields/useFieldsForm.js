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
import { reactive, onUnmounted } from 'vue'
import FieldsForm from '~/Core/resources/js/components/Fields/FieldsForm'

const forms = reactive({})

function purgeCache(formId) {
  delete forms[formId]
}

export function useForm(formId) {
  return forms[formId]
}

export { purgeCache }

export function useFieldsForm(fields, data = {}, options = {}, formId = null) {
  const form = reactive(new FieldsForm(fields, data, options, formId))

  forms[form.formId] = form

  onUnmounted(() => {
    purgeCache(form.formId)
  })

  return { form }
}
