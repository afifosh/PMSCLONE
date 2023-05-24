import { reactive } from 'vue'
import Form from './services/Form/Form'

export function useForm(data = {}, options = {}) {
  const form = reactive(new Form(data, options))

  return { form }
}
