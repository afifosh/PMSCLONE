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
import i18n from '~/Core/resources/js/i18n'

import ContactIndex from './views/Contacts/ContactIndex.vue'
import CreateContact from './views/Contacts/CreateContact.vue'
import ViewContact from './views/Contacts/ViewContact.vue'

import CompanyIndex from './views/Companies/CompanyIndex.vue'
import CreateCompany from './views/Companies/CreateCompany.vue'
import ViewCompany from './views/Companies/ViewCompany.vue'

import CreateDeal from '~/Deals/resources/js/views/CreateDeal.vue'

export default [
  {
    path: '/companies',
    name: 'company-index',
    component: CompanyIndex,
    meta: {
      title: i18n.t('contacts::company.companies'),
      initialize: false,
    },
    beforeEnter: (to, from) => {
      to.meta.initialize = to.name === 'company-index'
    },
    children: [
      {
        path: 'create',
        name: 'create-company',
        components: {
          create: CreateCompany,
        },
        meta: { title: i18n.t('contacts::company.create') },
      },
    ],
  },
  {
    path: '/companies/:id',
    name: 'view-company',
    component: ViewCompany,
    children: [
      {
        path: 'contacts/create',
        component: CreateContact,
        name: 'createContactViaCompany',
      },
      {
        path: 'deals/create',
        component: CreateDeal,
        name: 'createDealViaCompany',
      },
    ].map(route => Object.assign(route, { meta: { scrollToTop: false } })),
  },
  // contact routes
  {
    path: '/contacts',
    name: 'contact-index',
    component: ContactIndex,
    meta: {
      title: i18n.t('contacts::contact.contacts'),
      initialize: false,
    },
    beforeEnter: (to, from) => {
      to.meta.initialize = to.name === 'contact-index'
    },
    children: [
      {
        path: 'create',
        name: 'create-contact',
        components: {
          create: CreateContact,
        },
        meta: { title: i18n.t('contacts::contact.create') },
      },
    ],
  },
  {
    path: '/contacts/:id',
    name: 'view-contact',
    component: ViewContact,
    children: [
      {
        path: 'companies/create',
        component: CreateCompany,
        name: 'createCompanyViaContact',
      },
      {
        path: 'deals/create',
        component: CreateDeal,
        name: 'createDealViaContact',
      },
    ].map(route => Object.assign(route, { meta: { scrollToTop: false } })),
  },
]
