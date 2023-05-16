<template>
  <ITabPanel @activated.once="loadData" :lazy="!dataLoadedFirstTime">
    <div
      class="-mt-[20px] mb-3 overflow-hidden rounded-b-md border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900 sm:mb-7"
    >
      <div class="px-4 py-5 sm:p-6" v-show="!showCreateForm">
        <div class="sm:flex sm:items-start sm:justify-between">
          <div>
            <h3
              class="text-base/6 font-medium text-neutral-900 dark:text-white"
              v-t="'activities::activity.manage_activities'"
            />
            <div
              class="mt-2 max-w-xl text-sm text-neutral-500 dark:text-neutral-200"
            >
              <p v-t="'activities::activity.info'" />
            </div>
          </div>
          <div
            class="mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center"
          >
            <IButton
              @click="showCreateForm = true"
              size="sm"
              icon="Plus"
              :text="$t('activities::activity.add')"
            />
          </div>
        </div>
        <FormInputSearch
          class="mt-2"
          v-model="search"
          v-show="hasActivities || search"
          @input="performSearch($event, associateable)"
        />
      </div>
      <CreateActivity
        v-if="showCreateForm"
        :shadow="false"
        :ring="false"
        :rounded="false"
        :via-resource="resourceName"
        @cancel="showCreateForm = false"
      />
    </div>

    <div class="sm:block">
      <div
        class="border-b border-neutral-200 dark:border-neutral-600"
        v-show="hasActivities"
      >
        <div class="flex items-center justify-center">
          <nav
            class="overlow-y-hidden -mb-px flex grow snap-x snap-mandatory overflow-x-auto sm:grow-0 sm:space-x-4 lg:space-x-6"
          >
            <a
              v-for="filter in filters"
              :key="filter.id"
              @click.prevent="activateFilter(filter)"
              href="#"
              :class="[
                activeFilter === filter.id
                  ? 'border-neutral-700 text-neutral-700 dark:border-neutral-400 dark:text-neutral-200'
                  : 'border-transparent text-neutral-500 hover:border-neutral-300 hover:text-neutral-700 dark:text-neutral-100 dark:hover:border-neutral-500 dark:hover:text-neutral-300',
                'group inline-flex min-w-full shrink-0 snap-start snap-always items-center justify-center whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium sm:min-w-0',
              ]"
            >
              {{ filter.title }} <span class="ml-2">({{ filter.total }})</span>
            </a>
          </nav>
        </div>
      </div>
    </div>

    <div class="py-2 sm:py-4">
      <p
        class="mt-6 flex items-center justify-center font-medium text-neutral-800 dark:text-neutral-200"
        v-if="isFilterDataEmpty"
      >
        <Icon
          :icon="activeFilterInstance.empty.icon"
          :class="['mr-2 h-5 w-5', activeFilterInstance.empty.iconClass]"
        />
        {{ activeFilterInstance.empty.text }}
      </p>

      <Activities
        :activities="activeFilterInstance.data"
        :via-resource="resourceName"
      />
    </div>

    <div
      class="mt-6 text-center text-neutral-800 dark:text-neutral-200"
      v-show="isPerformingSearch && !hasSearchResults"
      v-t="'core::app.no_search_results'"
    />

    <InfinityLoader
      @handle="infiniteHandler($event, associateable)"
      :scroll-element="scrollElement"
      ref="infinityRef"
    />
  </ITabPanel>
</template>
<script setup>
import { ref, computed } from 'vue'
import Activities from './RelatedActivityList.vue'
import CreateActivity from './RelatedActivityCreate.vue'
import InfinityLoader from '~/Core/resources/js/components/InfinityLoader.vue'
import { watchOnce } from '@vueuse/core'
import orderBy from 'lodash/orderBy'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import { useRecordTab } from '~/Core/resources/js/composables/useRecordTab'
import { useApp } from '~/Core/resources/js/composables/useApp'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useComments } from '~/Comments/resources/js/composables/useComments'
import { useGlobalEventListener } from '~/Core/resources/js/composables/useGlobalEventListener'

const props = defineProps({
  resourceName: { required: true, type: String },
  scrollElement: { type: String },
})

const associateable = 'activities'
const activeFilter = ref('all')
const infinityRef = ref(null)
const showCreateForm = ref(false)

const { appMoment, userTimezone } = useDates()
const { currentUser } = useApp()

const { t } = useI18n()
const route = useRoute()

const { commentsAreVisible } = useComments(
  route.query.resourceId,
  associateable
)

const {
  dataLoadedFirstTime,
  focusToAssociateableElement,
  searchResults,
  record,
  isPerformingSearch,
  hasSearchResults,
  performSearch,
  search,
  loadData,
  infiniteHandler,
  refresh,
} = useRecordTab({
  resourceName: props.resourceName,
  infinityRef,
  scrollElement: props.scrollElement,
  // Because of the filters badges totals, if the user has more then 15 activities, they won't be accurate
  perPage: 100,
})

const activeFilterInstance = computed(() =>
  filters.value.find(filter => filter.id === activeFilter.value)
)

const todaysActivities = computed(() =>
  incompleteActivities.value.filter(d =>
    createDueDateMoment(d.due_date).isSame(
      appMoment().clone().tz(userTimezone.value),
      'day'
    )
  )
)

const tomorrowActivities = computed(() =>
  incompleteActivities.value.filter(d =>
    createDueDateMoment(d.due_date).isSame(
      appMoment().clone().tz(userTimezone.value).add(1, 'day'),
      'day'
    )
  )
)

const thisWeekActivities = computed(() =>
  incompleteActivities.value.filter(d =>
    createDueDateMoment(d.due_date)
      .isoWeekday(Number(currentUser.value.first_day_of_week))
      .isSame(
        appMoment()
          .clone()
          .tz(userTimezone.value)
          .isoWeekday(Number(currentUser.value.first_day_of_week)),
        'week'
      )
  )
)

const nextWeekActivities = computed(() =>
  incompleteActivities.value.filter(d =>
    createDueDateMoment(d.due_date)
      .isoWeekday(Number(currentUser.value.first_day_of_week))
      .isSame(
        appMoment()
          .clone()
          .tz(userTimezone.value)
          .isoWeekday(Number(currentUser.value.first_day_of_week))
          .add(1, 'week'),
        'week'
      )
  )
)

/**
 * Get the activities for the resource ordered by not completed on top and by due date
 */
const activities = computed(() =>
  orderBy(
    searchResults.value || record.value.activities,
    [
      'is_completed',
      activity => createDueDateMoment(activity.due_date).toDate(),
    ],
    ['asc', 'asc']
  )
)

/**
 * Get the currently incomplete activities from the loaded activities
 */
const incompleteActivities = computed(() =>
  activities.value.filter(activity => !activity.is_completed)
)

/**
 * Get the currently completed activities from the loaded activities
 */
const completedActivities = computed(() =>
  activities.value.filter(activity => activity.is_completed)
)

const hasActivities = computed(() => activities.value.length > 0)

const isFilterDataEmpty = computed(
  () =>
    activeFilterInstance.value.total === 0 &&
    dataLoadedFirstTime.value &&
    !(isPerformingSearch.value && !hasSearchResults.value)
)

/**
 * Activate the given filter
 */
function activateFilter(filter) {
  activeFilter.value = filter.id
  loadData()
}

/**
 * Create Moment.js instance from the given due date Object
 */
function createDueDateMoment(date) {
  if (!date.time) {
    return appMoment(date.date)
  }

  return appMoment(date.date + ' ' + date.time + ':00')
    .clone()
    .tz(userTimezone.value)
}

/**
 * Handle resource record updated event
 *
 * We will use this function to retieve again the first page of the activities
 * for the current resource
 *
 * The check is performed e.q. if new activities are created from workflows, it won't be fetched
 * e.q. when deal stage is updated
 */
function resourceRecordUpdated(updatedRecord) {
  // When using preview modal it may not be the same resource
  if (Number(updatedRecord.id) === Number(record.value.id)) {
    refresh(associateable)
  }
}

const filters = computed(() => [
  {
    id: 'all',
    title: t('activities::activity.filters.all'),
    data: activities.value,
    total: activities.value.length,
    empty: {
      text: t('core::app.all_caught_up'),
      icon: 'Check',
      iconClass: 'text-success-500',
    },
  },
  {
    id: 'today',
    title: t('activities::activity.filters.today'),
    data: todaysActivities.value,
    total: todaysActivities.value.length,
    empty: {
      text: t('core::app.all_caught_up'),
      icon: 'Check',
      iconClass: 'text-success-500',
    },
  },
  {
    id: 'tomorrow',
    title: t('activities::activity.filters.tomorrow'),
    data: tomorrowActivities.value,
    total: tomorrowActivities.value.length,
    empty: {
      text: t('core::app.all_caught_up'),
      icon: 'Check',
      iconClass: 'text-success-500',
    },
  },
  {
    id: 'this_week',
    title: t('activities::activity.filters.this_week'),
    data: thisWeekActivities.value,
    total: thisWeekActivities.value.length,
    empty: {
      text: t('core::app.all_caught_up'),
      icon: 'Check',
      iconClass: 'text-success-500',
    },
  },
  {
    id: 'next_week',
    title: t('activities::activity.filters.next_week'),
    data: nextWeekActivities.value,
    total: nextWeekActivities.value.length,
    empty: {
      text: t('core::app.all_caught_up'),
      icon: 'Check',
      iconClass: 'text-success-500',
    },
  },
  {
    id: 'done',
    title: t('activities::activity.filters.done'),
    data: completedActivities.value,
    total: completedActivities.value.length,
    empty: {
      text: t('activities::activity.filters.done_empty_state'),
      icon: 'CheckCircle',
      iconClass: 'text-neutral-500 dark:text-neutral-300',
    },
  },
])

if (route.query.resourceId && route.query.section === associateable) {
  // Wait till the data is loaded for the first time and the
  // elements are added to the document so we can have a proper scroll
  watchOnce(dataLoadedFirstTime, () => {
    focusToAssociateableElement(
      associateable,
      route.query.resourceId,
      'activity'
    ).then(() => {
      if (route.query.comment_id) {
        commentsAreVisible.value = true
      }
    })
  })
}

useGlobalEventListener(
  `${props.resourceName}-record-updated`,
  resourceRecordUpdated
)
</script>
