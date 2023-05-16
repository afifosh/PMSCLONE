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

import { useActivityTypes } from './composables/useActivityTypes'
import SettingsActivities from './components/SettingsActivities.vue'

import ActivitiesTab from './components/RecordTabActivity.vue'
import ActivitiesTabPanel from './components/RecordTabActivityPanel.vue'
import RecordTabTimelineActivity from './components/RecordTabTimelineActivity.vue'

import ActivityTypeField from './fields/ActivityTypeField.vue'
import ActivityDueDateField from './fields/ActivityDueDateField.vue'
import ActivityEndDateField from './fields/ActivityEndDateField.vue'
import GuestsSelectField from './fields/GuestsSelectField.vue'

import CreateActivityModal from './components/CreateActivityModal.vue'

import MyActivitiesCard from './components/MyActivitiesCard.vue'

import routes from './routes'

const { setActivityTypes } = useActivityTypes()

if (window.Innoclapps) {
  Innoclapps.booting(function (Vue, router) {
    Vue.component('ActivityTypeField', ActivityTypeField)
      .component('ActivityDueDateField', ActivityDueDateField)
      .component('ActivityEndDateField', ActivityEndDateField)
      .component('GuestsSelectField', GuestsSelectField)
      .component('ActivitiesTab', ActivitiesTab)
      .component('ActivitiesTabPanel', ActivitiesTabPanel)
      .component('MyActivitiesCard', MyActivitiesCard)
      .component('RecordTabTimelineActivity', RecordTabTimelineActivity)
      .component('CreateActivityModal', CreateActivityModal)

    setActivityTypes(Innoclapps.config('activities.types') || [])

    // Routes
    routes.forEach(route => router.addRoute(route))

    router.addRoute('settings', {
      path: 'activities',
      name: 'activity-settings',
      component: SettingsActivities,
      meta: {
        title: i18n.t('activities::activity.activities'),
      },
    })
  })
}
