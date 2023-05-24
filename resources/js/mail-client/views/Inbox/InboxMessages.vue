<template>
  <ILayout :overlay="loadingAccounts">
    <template #actions>
      <NavbarSeparator class="hidden lg:block" />
      <div class="inline-flex items-center">
        <IMinimalDropdown
          type="horizontal"
          :placement="
            !(account.is_initial_sync_performed && !isSyncDisabled)
              ? 'bottom-end'
              : 'bottom'
          "
        >
          <IDropdownItem
            v-if="account.authorizations.update"
            :to="{
              name: `edit-email-account`,
              params: { id: account.id },
            }"
            :text="$t('mailclient::mail.account.edit')"
          />
          <IDropdownItem
            :to="{ name: 'email-accounts-index' }"
            :text="$t('mailclient::mail.account.manage')"
          />
        </IMinimalDropdown>
        <IButton
          v-if="account.is_initial_sync_performed && !isSyncDisabled"
          v-i-tooltip.left="$t('mailclient::inbox.synchronize')"
          variant="secondary"
          size="sm"
          class="ml-3 lg:ml-6"
          :disabled="syncInProgress"
          :loading="syncInProgress"
          @click="performCurrentAccountManualSync"
          icon="Refresh"
        />
      </div>
    </template>
    <div class="mx-auto max-w-7xl">
      <div
        v-if="!loadingAccounts && hasAccounts"
        class="grid grid-cols-12 gap-4"
      >
        <div class="col-span-12 lg:col-span-3">
          <div class="sm:sticky sm:top-2">
            <FormDropdownSelect
              @change="handleAccountSelected"
              adaptive-width
              :items="accounts"
              :modelValue="account"
              class="w-full"
              label-key="email"
            >
              <template v-slot="{ label, toggle }">
                <IButton
                  @click="toggle"
                  variant="white"
                  class="justify-between py-2.5"
                  :loading="syncInProgress"
                  block
                >
                  <span class="truncate font-medium">{{ label }}</span>
                  <Icon
                    icon="ChevronDown"
                    class="-mr-1 ml-2 h-5 w-5 shrink-0"
                  />
                </IButton>
              </template>
            </FormDropdownSelect>

            <IButton
              variant="primary"
              class="my-3"
              block
              icon="Mail"
              :disabled="!account.can_send_mails"
              @click="compose(true)"
              :text="$t('mailclient::mail.compose')"
            />

            <FoldersMenu :folders="account && account.active_folders_tree" />
          </div>
        </div>
        <div class="col-span-12 lg:col-span-9">
          <IAlert
            v-if="isSyncDisabled"
            class="mb-4 border border-warning-200"
            variant="warning"
          >
            {{ account.sync_state_comment }}
          </IAlert>

          <IAlert
            v-if="!account.sent_folder_id || !account.sent_folder"
            class="mb-4 border border-warning-200"
            variant="warning"
          >
            <p>Action required, select the sent folder for this account.</p>

            <router-link
              :to="{ name: 'edit-email-account', params: { id: account.id } }"
              class="font-medium text-warning-700 hover:text-warning-600"
            >
              Edit Account <span aria-hidden="true">&rarr;</span>
            </router-link>
          </IAlert>

          <IAlert
            v-if="!account.trash_folder_id || !account.trash_folder"
            class="mb-4 border border-warning-200"
            variant="warning"
          >
            <p>Action required, select the trash folder for this account.</p>
            <router-link
              :to="{ name: 'edit-email-account', params: { id: account.id } }"
              class="font-medium text-warning-700 hover:text-warning-600"
            >
              Edit Account <span aria-hidden="true">&rarr;</span>
            </router-link>
          </IAlert>

          <IAlert
            v-if="!account.is_initial_sync_performed"
            class="mb-4 border border-info-200"
          >
            {{ $t('mailclient::mail.initial_sync_info') }}
          </IAlert>

          <router-view name="message" :account="account" />

          <router-view
            v-if="hasActiveFolders"
            name="messages"
            :account="account"
            ref="messages"
          />
          <ICard v-else class="h-60">
            <div class="m-auto mt-8 block max-w-2xl text-center">
              <Icon icon="Folder" class="mx-auto h-12 w-12 text-neutral-400" />
              <p
                class="mt-1 text-sm text-neutral-500"
                v-t="'mailclient.mail.account.no_active_folders'"
              />
              <div class="mt-6 space-x-2">
                <IButton
                  v-if="account.authorizations.update"
                  variant="primary"
                  :to="{
                    name: 'edit-email-account',
                    params: { id: account.id },
                  }"
                  :text="$t('mailclient::mail.account.activate_folders')"
                />
                <IButton
                  :to="{ name: 'email-accounts-index' }"
                  variant="secondary"
                  :text="$t('mailclient::mail.account.manage')"
                />
              </div>
            </div>
          </ICard>
        </div>
      </div>
    </div>
    <Compose
      :visible="isComposing"
      :default-account="account"
      @modal-hidden="compose(false)"
    />
  </ILayout>
</template>
<script setup>
import { ref, computed, watch } from 'vue'
import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'
import FoldersMenu from './InboxMessagesFoldersMenu.vue'
import Compose from '../Emails/ComposeMessage.vue'
import { useStore } from 'vuex'
import { useGlobalEventListener } from '../../composables/useGlobalEventListener'

const route = useRoute()
const router = useRouter()
const store = useStore()

const loadingAccounts = ref(true)
const isComposing = ref(false)

watch(
  () => route.query.compose,
  newVal => {
    newVal && compose()
  }
)

/**
 * When navigating e.q. from message and directly clicking
 * on the MENU item INBOX, we need to trigger the initAccounts methods
 * as the accounts are not loaded nor redirecting to the messages route
 */
onBeforeRouteUpdate((to, from, next) => {
  if (to.name === 'inbox') {
    redirectToAccountMessages(account.value, to.query)
  } else {
    next()
  }
})

const accounts = computed(() => store.getters['emailAccounts/accounts'])

const hasAccounts = computed(() => accounts.value.length > 0)

const account = computed(
  () => store.getters['emailAccounts/activeInboxAccount']
)

const syncInProgress = computed(() => store.state.emailAccounts.syncInProgress)

const hasActiveFolders = computed(() => account.value.active_folders.length > 0)

const isSyncDisabled = computed(
  () => account.value.is_sync_stopped || account.value.is_sync_disabled
)

function setActiveAccount(account) {
  store.commit('emailAccounts/SET_INBOX_ACCOUNT', account)
}

function updateAccountInStore(data) {
  store.commit('emailAccounts/UPDATE', data)
}

function fetchAccounts(data) {
  return store.dispatch('emailAccounts/fetch', data)
}

function syncAccount(accountId) {
  return store.dispatch('emailAccounts/syncAccount', accountId)
}

function compose(boolean = true) {
  isComposing.value = boolean

  if (boolean == false) {
    let query = Object.assign({}, route.query)
    delete query.compose
    router.replace({ query })
  }
}

function handleAccountSelected(account) {
  setActiveAccount(account)
  redirectToAccountMessages(account)
}

function handleActionExecutedEvent(action) {
  // Makes sure to update the account after an action is executed
  // This will be update data like the folders unread count
  if (action.response.hasOwnProperty('account')) {
    store.commit('emailAccounts/UPDATE', {
      id: action.response.account.id,
      item: action.response.account,
    })
  }

  // Update global unread messages count
  if (action.response.hasOwnProperty('unread_count')) {
    store.dispatch(
      'emailAccounts/updateUnreadCountUI',
      action.response.unread_count
    )
  }
}

function redirectToAccountMessages(forAccount, query = {}) {
  let folderId = forAccount.active_folders[0]
    ? forAccount.active_folders[0].id
    : null

  // When account does not have active folders
  if (!folderId) {
    return
  }

  router.replace({
    name: 'inbox-messages',
    params: {
      account_id: forAccount.id,
      // Sets the first syncable folder as default
      folder_id: folderId,
    },
    query: { ...route.query, ...query },
  })
}

function handleSyncFinishedEvent() {
  initAccounts(true)
}

async function initAccounts(skipCache = false) {
  await fetchAccounts({
    force: skipCache,
  })

  if (!hasAccounts.value) {
    router.replace({
      name: 'email-accounts-index',
    })

    return
  }

  // Check if the account is configured when handleSyncFinishedEvent method calls this function
  if (route.params.account_id && route.params.folder_id) {
    setActiveAccount(Number(route.params.account_id))
  } else if (Object.keys(account.value).length === 0) {
    setActiveAccount(accounts.value[0])
  }

  // When accessing the INBOX route without any params
  // Redirect to the messages
  if (route.name === 'inbox' && hasActiveFolders.value) {
    redirectToAccountMessages(account.value)
  }
}

function performCurrentAccountManualSync() {
  syncAccount(account.value.id).then(data => {
    // Update the account in store in case of folder changes
    updateAccountInStore({
      id: data.id,
      item: data,
    })

    Innoclapps.$emit('user-synchronized-email-account', data)
  })
}

initAccounts()
  .then(() => route.query.compose && compose())
  .finally(() => (loadingAccounts.value = false))

useGlobalEventListener('action-executed', handleActionExecutedEvent)
useGlobalEventListener('email-accounts-sync-finished', handleSyncFinishedEvent)
</script>
