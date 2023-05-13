<template>
  <ITabPanel @activated.once="loadData" :lazy="!dataLoadedFirstTime">
    <div
      class="-mt-[20px] mb-3 rounded-b-md border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900 sm:mb-7"
    >
      <div class="px-4 py-5 sm:p-6">
        <div class="sm:flex sm:items-start sm:justify-between">
          <div>
            <h3
              class="text-base/6 font-medium text-neutral-900 dark:text-white"
              v-t="'mailclient.mail.manage_emails'"
            />
            <div
              class="mt-2 max-w-xl text-sm text-neutral-500 dark:text-neutral-200"
            >
              <p v-t="'mailclient.mail.info'" />
            </div>
          </div>
          <div
            class="mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center"
          >
            <div
              v-i-tooltip="
                !hasAccountsConfigured
                  ? $t('mailclient::mail.account.integration_not_configured')
                  : null
              "
            >
              <IButton
                @click="compose(true)"
                size="sm"
                icon="Plus"
                :disabled="!hasAccountsConfigured"
                :text="$t('mailclient::mail.create')"
              />
            </div>
          </div>
        </div>
        <div class="mt-3 text-sm">
          <router-link
            :to="{ name: 'email-accounts-index' }"
            class="link font-medium"
          >
            {{ $t('mailclient::mail.account.connect') }}
            <span aria-hidden="true"> &rarr;</span>
          </router-link>
        </div>

        <FormInputSearch
          class="mt-2"
          v-model="search"
          v-show="hasEmails || search"
          @input="performSearch($event, associateable)"
        />
      </div>
    </div>

    <Compose
      :visible="isComposing"
      :resource-name="resourceName"
      :resource-record="record"
      :to="to"
      ref="composeRef"
      @modal-hidden="compose(false)"
    />

    <Emails :emails="emails" :via-resource="resourceName" />

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
import { ref, computed, nextTick } from 'vue'
import { watchOnce } from '@vueuse/core'
import Compose from './ComposeMessage.vue'
import Emails from './RelatedMessageList.vue'
import InfinityLoader from '~/Core/resources/js/components/InfinityLoader.vue'
import orderBy from 'lodash/orderBy'
import { useRecordTab } from '~/Core/resources/js/composables/useRecordTab'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import { useGlobalEventListener } from '~/Core/resources/js/composables/useGlobalEventListener'

const props = defineProps({
  resourceName: { required: true, type: String },
  scrollElement: { type: String },
})

const store = useStore()
const route = useRoute()

const isComposing = ref(false)
const infinityRef = ref(null)
const composeRef = ref(null)
const associateable = 'emails'

const { addResourceRecordHasManyRelationship } = useRecordStore()

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
})

const hasAccountsConfigured = computed(
  () => store.getters['emailAccounts/hasConfigured']
)

const emails = computed(() =>
  orderBy(searchResults.value || record.value.emails, 'date', 'desc')
)

const hasEmails = computed(() => emails.value.length > 0)

const to = computed(() => {
  // First check if there is an email property in the resource record data
  if (record.value.email) {
    return createToArrayFromRecord(record.value, props.resourceName)
  }

  // Vue 3, before navigating, it's hitting this computed but there is no data
  // TODO, research more
  if (Object.keys(record.value).length === 0) {
    return []
  }

  // Next we will try to provide associations and email to send email from
  // the related resources, e.q. when viewing contact and the contact has no email
  // we will try to provide an email from the contact latest company and so on.
  if (props.resourceName === 'contacts') {
    if (record.value.companies[0]) {
      return createToArrayFromRecord(record.value.companies[0], 'companies')
    }
  } else if (props.resourceName === 'companies') {
    if (record.value.contacts[0]) {
      return createToArrayFromRecord(record.value.contacts[0], 'contacts')
    }
  } else if (props.resourceName === 'deals') {
    if (record.value.contacts[0]) {
      return createToArrayFromRecord(record.value.contacts[0], 'contacts')
    } else if (record.value.companies[0]) {
      return createToArrayFromRecord(record.value.companies[0], 'companies')
    }
  }

  return []
})

function createToArrayFromRecord(record, resourceName) {
  return record.email
    ? [
        {
          address: record.email,
          name: record.display_name,
          resourceName: resourceName,
          id: record.id,
        },
      ]
    : []
}

function compose(state = true) {
  isComposing.value = state

  if (state) {
    nextTick(() => composeRef.value.subjectRef.focus())
  }
}

function handleSent(email) {
  addResourceRecordHasManyRelationship(email, associateable)
}

if (route.query.resourceId && route.query.section === associateable) {
  // Wait till the data is loaded for the first time and the
  // elements are added to the document so we can have a proper scroll
  watchOnce(dataLoadedFirstTime, () => {
    focusToAssociateableElement(associateable, route.query.resourceId, 'email')
  })
}

useGlobalEventListener('email-accounts-sync-finished', refresh)
useGlobalEventListener('email-sent', handleSent)
</script>
