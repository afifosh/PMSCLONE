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
