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

import { useStorage } from '@vueuse/core'

import ActivityIndex from './views/ActivityIndex.vue'
import CreateActivity from './views/CreateActivity.vue'
import EditActivity from './views/EditActivity.vue'

import CalendarSync from './views/CalendarSync.vue'

const isCalendarDefaultView = useStorage(
  'activity-calendar-view-default',
  false
)

export default [
  {
    path: '/calendar/sync',
    name: 'calendar-sync',
    component: CalendarSync,
    meta: {
      title: i18n.t('activities::calendar.calendar_sync'),
    },
  },
  {
    path: '/activities/calendar',
    name: 'activity-calendar',
    component: () => import('./views/ActivityCalendar.vue'),
    meta: { title: i18n.t('activities::calendar.calendar') },
    beforeEnter: () => {
      isCalendarDefaultView.value = true
    },
  },
  {
    path: '/activities',
    name: 'activity-index',
    component: ActivityIndex,
    meta: {
      title: i18n.t('activities::activity.activities'),
      subRoutes: ['create-activity', 'edit-activity', 'view-activity'],
      calendarRoute: 'activity-calendar',
      initialize: false,
    },
    beforeEnter: async (to, from) => {
      if (
        isCalendarDefaultView.value &&
        from.name != to.meta.calendarRoute &&
        to.meta.subRoutes.indexOf(to.name) === -1 &&
        // The calendar does not have filters, hence, it's not supported
        // for this reason, we will show the table view
        !to.query.filter_id
      ) {
        return { name: to.meta.calendarRoute }
      }

      to.meta.initialize = to.name === 'activity-index'

      if (to.meta.subRoutes.indexOf(to.name) === -1 && !to.query.filter_id) {
        isCalendarDefaultView.value = false
      }
    },
    children: [
      {
        path: 'create',
        name: 'create-activity',
        components: {
          create: CreateActivity,
        },
        meta: { title: i18n.t('activities::activity.create') },
      },
      {
        path: ':id',
        name: 'view-activity',
        meta: {
          scrollToTop: false,
        },
        components: {
          edit: EditActivity,
        },
      },
      {
        path: ':id/edit',
        name: 'edit-activity',
        meta: {
          scrollToTop: false,
        },
        components: {
          edit: EditActivity,
        },
      },
    ],
  },
]
