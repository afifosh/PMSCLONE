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
import routes from './routes'

import RecordStore from './store/Record'
import FieldsStore from './store/Fields'
import TableStore from './store/Table'
import FiltersStore from './store/Filters'
import RecordPreviewStore from './store/RecordPreview'

if (window.Innoclapps) {
  Innoclapps.booting(function (Vue, router, store) {
    store.registerModule('record', RecordStore)
    store.registerModule('fields', FieldsStore)
    store.registerModule('table', TableStore)
    store.registerModule('filters', FiltersStore)
    store.registerModule('recordPreview', RecordPreviewStore)

    // Routes
    routes.forEach(route => router.addRoute(route))
  })
}
