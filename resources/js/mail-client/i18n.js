import { createI18n } from 'vue-i18n'
import { resolveValue } from '@intlify/core-base'
import { getLocale } from '@/utils'

// Allow same syntax for backend and front-end
function messageResolver(obj, path) {
  return resolveValue(obj, path.replace('::', '.'))
}

const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: getLocale(),
  fallbackLocale: 'en',
  messageResolver,
  messages: lang,
})

export default {
  instance: i18n,
  t: i18n.global.t,
}
