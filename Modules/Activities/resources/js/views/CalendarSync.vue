<template>
  <ILayout>
    <div class="mx-auto max-w-5xl">
      <ICard :overlay="!componentReady">
        <template #header>
          <ICardHeading class="flex items-center">
            {{ $t('activities::calendar.calendar_sync') }}
            <IBadge
              v-if="
                calendar &&
                !calendar.is_sync_disabled &&
                !calendar.is_sync_stopped
              "
              variant="success"
              wrapper-class="ml-2"
            >
              <Icon icon="Refresh" class="mr-1 h-3 w-3" />
              {{
                calendar.is_synchronizing_via_webhook ? 'Webhook' : 'Polling'
              }}
            </IBadge>
          </ICardHeading>
        </template>
        <IAlert
          v-if="oAuthCalendarsFetchError"
          @dismissed="oAuthCalendarsFetchError = null"
          variant="warning"
          dismissible
          class="mb-4"
        >
          {{ oAuthCalendarsFetchError }}
        </IAlert>
        <div
          v-if="
            (!calendar || (calendar && calendar.is_sync_disabled)) &&
            !accountConnectionInProgress
          "
        >
          <ConnectAccount />
          <IButton
            variant="secondary"
            v-i-modal="'calendarConnectNewAccount'"
            :text="$t('core::oauth.add')"
            :class="{ 'mr-1': hasOAuthAccounts }"
          />
          <span
            v-show="hasOAuthAccounts"
            class="mt-3 block text-neutral-800 dark:text-neutral-300 sm:mt-0 sm:inline"
            v-t="'core::oauth.or_choose_existing'"
          />
          <div
            v-if="hasOAuthAccounts && !accountConnectionInProgress"
            class="mt-4"
          >
            <OAuthAccount
              v-for="account in oAuthAccounts"
              :key="account.id"
              class="mb-3"
              :account="account"
            >
              <IButton
                size="sm"
                variant="secondary"
                class="ml-2"
                :disabled="account.requires_auth"
                :text="$t('core::oauth.connect')"
                @click="connect(account)"
              />
            </OAuthAccount>
          </div>
        </div>
        <div
          v-if="
            accountConnectionInProgress ||
            (calendar && !calendar.is_sync_disabled)
          "
        >
          <OAuthAccount
            v-if="calendar && !calendar.is_sync_disabled && calendar.account"
            class="mb-5"
            :account="calendar.account"
          >
            <IButton
              size="sm"
              variant="danger"
              class="ml-2"
              @click="stopSync"
              :disabled="syncStopInProgress"
              :loading="syncStopInProgress"
            >
              {{
                calendar.is_sync_stopped
                  ? $t('core::app.cancel')
                  : $t('core::oauth.stop_syncing')
              }}
            </IButton>
            <template #after-name v-if="calendar.sync_state_comment">
              <span
                class="text-sm text-danger-500"
                v-text="calendar.sync_state_comment"
              />
            </template>
          </OAuthAccount>
          <div class="mb-3">
            <p
              v-if="!calendar || (calendar && calendar.is_sync_disabled)"
              class="mb-6 text-neutral-800 dark:text-neutral-100"
            >
              {{
                $t('activities::calendar.account_being_connected', {
                  email: accountConnectionInProgress.email,
                })
              }}
            </p>
            <div class="grid grid-cols-12 gap-1 lg:gap-6">
              <div
                class="col-span-12 self-start lg:col-span-3 lg:flex lg:items-center lg:justify-end"
              >
                <p
                  class="font-medium text-neutral-800 dark:text-neutral-100 lg:text-sm"
                  v-t="'activities::calendar.calendar'"
                />
              </div>

              <div class="col-span-12 lg:col-span-4">
                <ICustomSelect
                  :options="availableOAuthCalendars"
                  :modelValue="selectedCalendar"
                  label="title"
                  :disabled="connectedOAuthAccountRequiresAuthentication"
                  :placeholder="
                    oAuthAccountCalendarsFetchRequestInProgress
                      ? $t('core::app.loading')
                      : ''
                  "
                  @option:selected="form.calendar_id = $event.id"
                  :clearable="false"
                />
                <IFormText
                  v-t="'activities::calendar.sync_support_only_primary'"
                  class="mt-2"
                />
                <IFormError v-text="form.getError('calendar_id')" />
              </div>
            </div>
          </div>

          <div class="mb-3">
            <div class="grid grid-cols-12 gap-1 lg:gap-6">
              <div
                class="col-span-12 lg:col-span-3 lg:flex lg:items-center lg:justify-end"
              >
                <p
                  class="font-medium text-neutral-800 dark:text-neutral-100 lg:text-sm"
                  v-t="'activities::calendar.save_events_as'"
                />
              </div>
              <div class="col-span-12 lg:col-span-4">
                <ICustomSelect
                  :options="activityTypesByName"
                  :modelValue="selectedActivityTypeValue"
                  label="name"
                  :clearable="false"
                  @option:selected="form.activity_type_id = $event.id"
                />
                <IFormError v-text="form.getError('activity_type_id')" />
              </div>
            </div>
          </div>
          <div class="grid grid-cols-12 gap-1 lg:gap-6">
            <div class="col-span-12 lg:col-span-3 lg:text-right">
              <p
                class="font-medium text-neutral-800 dark:text-neutral-100 lg:text-sm"
                v-t="'activities::calendar.sync_activity_types'"
              />
            </div>
            <div class="col-span-12 lg:col-span-4">
              <IFormCheckbox
                v-for="activityType in activityTypesByName"
                :key="activityType.id"
                v-model:checked="form.activity_types"
                :value="activityType.id"
                :label="activityType.name"
                name="activity_types"
              />
              <IFormError v-text="form.getError('activity_types')" />
            </div>
          </div>
        </div>
        <template
          #footer
          v-if="
            accountConnectionInProgress ||
            (calendar && !calendar.is_sync_disabled)
          "
        >
          <div>
            <div class="flex flex-col lg:flex-row lg:items-center">
              <div class="mb-2 grow lg:mb-0">
                <span
                  v-if="startSyncFromText"
                  class="text-sm text-neutral-800 dark:text-neutral-100"
                >
                  <Icon
                    icon="ExclamationTriangle"
                    class="-mt-1 mr-1 inline-flex h-5 w-5"
                  />
                  {{ startSyncFromText }}
                </span>
              </div>
              <div class="space-x-2">
                <IButton
                  v-if="
                    !calendar ||
                    (calendar && calendar.is_sync_disabled) ||
                    calendar.is_sync_stopped
                  "
                  @click="accountConnectionInProgress = null"
                  :disabled="form.busy"
                  :text="$t('core::app.cancel')"
                />
                <IButton
                  v-show="!calendar || (calendar && !calendar.is_sync_stopped)"
                  variant="secondary"
                  :disabled="form.busy"
                  :loading="form.busy"
                  @click="save"
                >
                  {{
                    !calendar ||
                    (calendar && calendar.is_sync_disabled) ||
                    calendar.is_sync_stopped
                      ? $t('core::oauth.start_syncing')
                      : $t('core::app.save')
                  }}
                </IButton>
                <IButton
                  v-show="calendar && calendar.is_sync_stopped"
                  variant="secondary"
                  :disabled="
                    form.busy || connectedOAuthAccountRequiresAuthentication
                  "
                  :loading="form.busy"
                  :text="$t('activities::calendar.reconfigure')"
                  @click="save"
                />
              </div>
            </div>
          </div>
        </template>
      </ICard>
    </div>
  </ILayout>
</template>
<script setup>
import { ref, computed } from 'vue'
import OAuthAccount from '~/Core/resources/js/views/OAuth/OAuthAccount.vue'
import ConnectAccount from './CalendarSyncConnectAccount.vue'
import orderBy from 'lodash/orderBy'
import filter from 'lodash/filter'
import { useI18n } from 'vue-i18n'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useRoute } from 'vue-router'
import { useForm } from '~/Core/resources/js/composables/useForm'
import { useActivityTypes } from '../composables/useActivityTypes'

const { t } = useI18n()
const route = useRoute()

const { localizedDateTime } = useDates()

const componentReady = ref(false)
const oAuthAccountCalendarsFetchRequestInProgress = ref(false)
const oAuthCalendarsFetchError = ref(null)
const accountConnectionInProgress = ref(null)
const calendar = ref(null)
const syncStopInProgress = ref(false)
const oAuthAccounts = ref([])
const availableOAuthCalendars = ref([])

const { form } = useForm()

const { typesByName: activityTypesByName } = useActivityTypes()

const hasOAuthAccounts = computed(() => oAuthAccounts.value.length > 0)

const selectedActivityTypeValue = computed(() =>
  activityTypesByName.value.find(type => type.id == form.activity_type_id)
)

const selectedCalendar = computed(() =>
  availableOAuthCalendars.value.find(
    calendar => calendar.id == form.calendar_id
  )
)

const connectedOAuthAccountRequiresAuthentication = computed(() => {
  if (!calendar.value || !calendar.value.account) {
    return false
  }

  return calendar.value.account.requires_auth
})

const startSyncFromText = computed(() => {
  // No connection nor calendar, do nothing
  if (
    (!accountConnectionInProgress.value && !calendar.value) ||
    (calendar.value && calendar.value.is_sync_stopped)
  ) {
    return
  }

  // If the calendar is not yet created, this means that we don't have any
  // sync history and just will show that only future events will be synced for the selected calendar
  if (!calendar.value) {
    return t('activities::calendar.only_future_events_will_be_synced')
  }

  // Let's try to find if the current selected calendar was previously configured
  // as calendar to sync, if found, the initial start_sync_from will be used to actual start syncing the events
  // in case if there were previously synced events and then the user changed the calendar and now want to get back again // on this calendar, we need to sync the previously synced events as well
  const previouslyUsedCalendar = filter(calendar.value.previously_used, [
    'calendar_id',
    form.calendar_id,
  ])

  // User does not want to sync and he is just looking at the configuration screen
  // for a configured account to sync, in this case, we will just show from what date the events are synced
  if (
    calendar.value.calendar_id === form.calendar_id &&
    !accountConnectionInProgress.value
  ) {
    return t('activities::calendar.events_being_synced_from', {
      date: localizedDateTime(calendar.value.start_sync_from),
    })
  }

  // Finally, we will check if there is account connection in progress or the actual form
  // calendar id is not equal with the currrent calendar id that the user selected
  if (
    accountConnectionInProgress.value ||
    calendar.value.calendar_id !== form.calendar_id
  ) {
    // If history found, use the start_sync_from date from the history
    if (previouslyUsedCalendar.length > 0) {
      return t('activities::calendar.events_will_sync_from', {
        date: localizedDateTime(previouslyUsedCalendar[0].start_sync_from),
      })
    } else if (calendar.value.calendar_id === form.calendar_id) {
      // Otherwise the user has selected a calendar that was first time selected and now we will just use
      // the start_sync_from date from the first time when the calendar was connected
      return t('activities::calendar.events_will_sync_from', {
        date: localizedDateTime(calendar.value.start_sync_from),
      })
    } else {
      return t('activities::calendar.only_future_events_will_be_synced')
    }
  }
})

function getLatestCreatedOAuthAccount() {
  return orderBy(oAuthAccounts.value, account => new Date(account.created_at), [
    'desc',
  ])[0]
}

function setInitialForm() {
  form.clear().set({
    access_token_id: null,
    activity_type_id: Innoclapps.config('activities.default_activity_type_id'),
    activity_types: activityTypesByName.value.map(type => type.id),
    calendar_id: null,
  })
}

/**
 * Start account sync connection
 */
function connect(account) {
  accountConnectionInProgress.value = account
  form.fill('access_token_id', account.id)

  retrieveOAuthAccountCalendars(account.id).then(oAuthCalendars => {
    form.set('calendar_id', oAuthCalendars[0].id)
  })
}

function save() {
  form.post('/calendar/account').then(connectedCalendar => {
    calendar.value = connectedCalendar
    accountConnectionInProgress.value = null
  })
}

function stopSync() {
  syncStopInProgress.value = true
  Innoclapps.request()
    .delete('/calendar/account')
    .then(({ data }) => {
      calendar.value = data
      accountConnectionInProgress.value = null
      setInitialForm()
    })
    .finally(() => (syncStopInProgress.value = false))
}

async function retrieveOAuthAccountCalendars(id) {
  oAuthAccountCalendarsFetchRequestInProgress.value = true
  oAuthCalendarsFetchError.value = null
  try {
    let { data } = await Innoclapps.request().get(`/calendars/${id}`)
    availableOAuthCalendars.value = data

    return data
  } catch (error) {
    oAuthCalendarsFetchError.value = error.response.data.message
    throw error
  } finally {
    oAuthAccountCalendarsFetchRequestInProgress.value = false
  }
}

function fillForm(forCalendar) {
  form.set({
    activity_type_id: forCalendar.activity_type_id,
    activity_types: forCalendar.activity_types,
    // Perhaps account deleted?
    access_token_id: forCalendar.account ? forCalendar.account.id : null,
  })
}

setInitialForm()

Promise.all([
  Innoclapps.request().get('oauth/accounts'),
  Innoclapps.request().get('calendar/account'),
]).then(values => {
  oAuthAccounts.value = values[0].data
  calendar.value = values[1].data

  if (calendar.value) {
    fillForm(calendar.value)
  }

  if (route.query.viaOAuth) {
    connect(getLatestCreatedOAuthAccount())
  } else if (
    calendar.value.account &&
    !connectedOAuthAccountRequiresAuthentication.value
  ) {
    // perhaps deleted or requires auth?
    retrieveOAuthAccountCalendars(calendar.value.account.id).then(
      oAuthCalendars => {
        form.set('calendar_id', calendar.value.calendar_id)
      }
    )
  }

  componentReady.value = true
})
</script>
