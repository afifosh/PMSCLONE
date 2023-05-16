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
import EmailsTab from './views/Emails/RecordTabEmails.vue'
import EmailsTabPanel from './views/Emails/RecordTabEmailsPanel.vue'
import RecordTabTimelineEmail from './components/RecordTabTimelineEmail.vue'
import MailEditorField from './components/MailEditorField.vue'
import { usePrivateChannel } from '~/Core/resources/js/composables/useBroadcast'

import routes from './routes'
import EmailAccountsStore from './store/EmailAccounts'

if (window.Innoclapps) {
  Innoclapps.booting((Vue, router, store) => {
    Vue.component('EmailsTab', EmailsTab)
    Vue.component('EmailsTabPanel', EmailsTabPanel)
    Vue.component('RecordTabTimelineEmail', RecordTabTimelineEmail)
    Vue.component('MailEditorField', MailEditorField)

    store.registerModule('emailAccounts', EmailAccountsStore)

    routes.forEach(route => router.addRoute(route))

    listenForEmailAccountSync(window.Innoclapps, store)
  })
}

/**
 * Listen when email accounts sync is finished.
 */
function listenForEmailAccountSync(app, store) {
  usePrivateChannel(
    'inbox',
    '.Modules\\MailClient\\Events\\EmailAccountsSyncFinished',
    e => {
      app.$emit('email-accounts-sync-finished', e)

      app
        .request()
        .get('mail/accounts/unread')
        .then(({ data }) =>
          store.dispatch('emailAccounts/updateUnreadCountUI', data)
        )
    }
  )
}
