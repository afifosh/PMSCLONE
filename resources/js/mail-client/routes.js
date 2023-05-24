import i18n from './i18n'

import Inbox from './views/Inbox/InboxMessages.vue'
import InboxMessages from './views/Inbox/Messages/InboxMessagesTable.vue'
import InboxMessage from './views/Inbox/Messages/InboxMessage.vue'

import EmailAccountsIndex from './views/Accounts/EmailAccountIndex.vue'
import EmailAccountCreate from './views/Accounts/CreateEmailAccount.vue'
import EmailAccountEdit from './views/Accounts/EditEmailAccount.vue'

import { createWebHistory, createRouter } from "vue-router";

const routes = [
  {
    path: '/inbox',
    name: 'inbox',
    component: Inbox,
    meta: {
      title: i18n.t('mailclient::inbox.inbox'),
    },
    children: [
      {
        path: ':account_id/folder/:folder_id/messages',
        components: {
          messages: InboxMessages,
        },
        name: 'inbox-messages',
        meta: {
          title: i18n.t('mailclient::inbox.inbox'),
        },
      },
      {
        path: ':account_id/folder/:folder_id/messages/:id',
        components: {
          message: InboxMessage,
        },
        name: 'inbox-message',
        meta: {
          scrollToTop: false,
        },
      },
    ],
  },
  {
    path: '/mail/accounts',
    name: 'email-accounts-index',
    component: EmailAccountsIndex,
    meta: {
      title: i18n.t('mailclient::mail.account.accounts'),
    },
    children: [
      {
        path: 'create',
        name: 'create-email-account',
        component: EmailAccountCreate,
        meta: { title: i18n.t('mailclient::mail.account.create') },
      },
      {
        path: ':id/edit',
        name: 'edit-email-account',
        component: EmailAccountEdit,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
