import { reactive, onUnmounted } from 'vue'
import FieldsForm from './FieldsForm'

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
