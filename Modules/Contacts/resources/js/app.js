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

import SettingsCompanies from './components/SettingsCompanies.vue'
import CompanyPreview from './components/PreviewCompany.vue'
import ContactPreview from './components/PreviewContact.vue'
import PhoneField from './components/PhoneField.vue'
import TableDataPhonesColumn from './components/TableDataPhonesColumn.vue'
import CreateContactModal from './components/CreateContactModal.vue'
import CreateCompanyModal from './components/CreateCompanyModal.vue'

import routes from './routes'

if (window.Innoclapps) {
  Innoclapps.booting(function (Vue, router) {
    Vue.component('CompanyPreview', CompanyPreview)
    Vue.component('ContactPreview', ContactPreview)
    Vue.component('PhoneField', PhoneField)
    Vue.component('TableDataPhonesColumn', TableDataPhonesColumn)
    Vue.component('CreateCompanyModal', CreateCompanyModal)
    Vue.component('CreateContactModal', CreateContactModal)

    // Routes
    routes.forEach(route => router.addRoute(route))

    router.addRoute('settings', {
      path: 'companies',
      component: SettingsCompanies,
      name: 'settings-companies',
      meta: { title: i18n.t('contacts::company.companies') },
    })
  })
}
