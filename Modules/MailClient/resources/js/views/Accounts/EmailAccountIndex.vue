<template>
  <ILayout>
    <template #actions>
      <NavbarSeparator v-show="hasAccounts" class="hidden lg:block" />

      <IButton
        v-show="hasAccounts"
        variant="primary"
        size="sm"
        :to="{ name: 'inbox' }"
        :text="$t('mailclient::inbox.inbox')"
      />
    </template>

    <ICard
      v-show="hasAccounts"
      no-body
      :overlay="isLoading"
      class="mx-auto max-w-7xl"
      :header="$t('mailclient::mail.account.accounts')"
      actions-class="w-full sm:w-auto"
    >
      <template #actions>
        <div
          class="mt-4 flex w-full flex-col space-y-1 sm:mt-0 sm:w-auto sm:flex-row sm:justify-end sm:space-x-2 sm:space-y-0"
        >
          <span
            class="grid sm:inline"
            v-i-tooltip="
              $gate.isRegularUser()
                ? $t('users::user.not_authorized')
                : $t('mailclient::mail.account.create_shared_info')
            "
          >
            <IButton
              size="sm"
              variant="white"
              icon="Share"
              :disabled="$gate.isRegularUser()"
              @click="createShared"
              :text="$t('mailclient::mail.account.connect_shared')"
            />
          </span>

          <IButton
            size="sm"
            icon="User"
            variant="white"
            :text="$t('mailclient::mail.account.connect_personal')"
            @click="createPersonal"
          />
        </div>
      </template>

      <TransitionGroup
        class="divide-y divide-neutral-200 dark:divide-neutral-700"
        name="flip-list"
        tag="ul"
      >
        <li v-for="account in accounts" :key="account.id">
          <div
            v-if="account.is_sync_stopped || account.requires_auth"
            class="flex items-center justify-between border-b border-warning-100 bg-warning-50 px-4 py-2"
          >
            <div class="text-sm font-medium text-warning-700">
              <p
                v-if="account.requires_auth"
                v-t="'core::oauth.requires_authentication'"
              />
              <p
                v-if="account.is_sync_stopped"
                v-text="account.sync_state_comment"
              />
            </div>

            <IButtonMinimal
              v-if="account.requires_auth"
              size="sm"
              variant="warning"
              class="shrink-0 self-start"
              :text="$t('core::oauth.re_authenticate')"
              @click="reAuthenticate(account)"
            />
          </div>
          <div
            :class="[
              'flex items-center px-4 py-4 sm:px-6',
              {
                'opacity-70':
                  account.is_sync_stopped || account.is_sync_disabled,
              },
            ]"
          >
            <div
              class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between"
            >
              <div class="truncate">
                <div class="flex text-sm">
                  <component
                    :is="account.authorizations.update ? 'a' : 'p'"
                    @click="account.authorizations.update ? edit(account) : ''"
                    :class="[
                      'truncate font-medium text-primary-600 dark:text-primary-400',
                      account.authorizations.update ? 'cursor-pointer' : '',
                    ]"
                  >
                    {{ account.email }}
                  </component>
                </div>
                <div class="mt-2 flex">
                  <div class="flex items-center text-sm">
                    <Icon
                      icon="Mail"
                      class="mr-1.5 h-5 w-5 shrink-0 text-neutral-400"
                    />
                    <p
                      class="mr-3 text-neutral-600 dark:text-neutral-300"
                      v-text="account.connection_type"
                    />
                    <IBadge
                      :variant="account.is_personal ? 'neutral' : 'info'"
                      :text="$t('mailclient::mail.account.' + account.type)"
                    />
                  </div>
                </div>
              </div>
              <div class="mt-4 shrink-0 sm:ml-5 sm:mt-0">
                <div class="flex space-x-3">
                  <IFormToggle
                    @change="togglePrimaryState($event, account)"
                    :disabled="account.is_sync_stopped"
                    :modelValue="account.is_primary"
                    :label="$t('mailclient::mail.account.is_primary')"
                  />
                  <IFormToggle
                    @change="toggleDisabledSyncState(account)"
                    v-show="
                      !account.is_sync_stopped && account.authorizations.update
                    "
                    :modelValue="account.is_sync_disabled"
                    :label="$t('mailclient::mail.disable_sync')"
                  />
                </div>
              </div>
            </div>
            <div
              class="ml-5 shrink-0 self-start sm:self-auto"
              v-if="
                account.authorizations.update || account.authorizations.delete
              "
            >
              <IMinimalDropdown>
                <IDropdownItem
                  v-if="account.authorizations.update"
                  @click="edit(account)"
                  :text="$t('core::app.edit')"
                />
                <IDropdownItem
                  v-if="account.authorizations.delete"
                  @click="destroy(account.id)"
                  :text="$t('core::app.delete')"
                />
              </IMinimalDropdown>
            </div>
          </div>
        </li>
      </TransitionGroup>
    </ICard>

    <IOverlay v-if="!hasAccounts" :show="isLoading" class="m-auto max-w-5xl">
      <div v-show="!isLoading" class="mx-auto max-w-2xl p-4 sm:p-6">
        <h2
          class="text-xl font-medium text-neutral-800 dark:text-neutral-200"
          v-t="'mailclient.mail.account.no_accounts_configured'"
        />

        <p
          class="text-sm text-neutral-500 dark:text-neutral-300"
          v-t="'mailclient.mail.account.no_accounts_configured_info'"
        />

        <ul
          role="list"
          class="mt-6 grid grid-cols-1 gap-6 border-y border-neutral-200 py-6 dark:border-neutral-600 lg:grid-cols-2"
        >
          <li
            v-for="(item, itemIdx) in emptyStateItems"
            :key="itemIdx"
            class="flow-root"
          >
            <div
              class="relative -m-2 flex items-center space-x-4 rounded-xl p-2"
            >
              <div
                class="flex h-12 w-14 shrink-0 items-center justify-center rounded-lg border border-primary-200 bg-primary-50"
              >
                <Icon :icon="item.icon" class="h-6 w-6 text-primary-600" />
              </div>
              <div>
                <p
                  class="mt-1 text-sm text-neutral-600 dark:text-neutral-200"
                  v-text="item.description"
                />
              </div>
            </div>
          </li>
        </ul>
        <div class="mt-6 space-y-1 sm:space-x-2">
          <span
            class="inline-block"
            v-i-tooltip="
              $gate.isRegularUser()
                ? $t('users::user.not_authorized')
                : $t('mailclient::mail.account.create_shared_info')
            "
          >
            <IButton
              variant="primary"
              @click="createShared"
              class="sm:w-justify-around w-full justify-center sm:w-auto"
              :disabled="$gate.isRegularUser()"
              icon="Share"
              :text="$t('mailclient::mail.account.connect_shared')"
            />
          </span>

          <IButton
            variant="primary"
            @click="createPersonal"
            icon="User"
            class="sm:w-justify-around w-full justify-center sm:w-auto"
            :text="$t('mailclient::mail.account.connect_personal')"
          />
        </div>
      </div>
    </IOverlay>
    <router-view></router-view>
  </ILayout>
</template>
<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { useLoader } from '~/Core/resources/js/composables/useLoader'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()
const store = useStore()

const { setLoading, isLoading } = useLoader(true)

const accounts = computed(() => store.getters['emailAccounts/accounts'])
const latestAccount = computed(() => store.getters['emailAccounts/latest'])
const hasAccounts = computed(() => accounts.value.length > 0)

const emptyStateItems = [
  {
    description: t('mailclient::mail.account.featured.sync'),
    icon: 'Refresh',
  },
  {
    description: t('mailclient::mail.account.featured.save_time'),
    icon: 'DocumentAdd',
  },
  {
    description: t('mailclient::mail.account.featured.placeholders'),
    icon: 'CodeBracket',
  },
  {
    description: t('mailclient::mail.account.featured.signature'),
    icon: 'Pencil',
  },
  {
    description: t('mailclient::mail.account.featured.associations', {
      resources:
        t('contacts::contact.contacts') +
        ', ' +
        t('contacts::company.companies'),
      resource: t('deals::deal.deals'),
    }),
    icon: 'Mail',
  },
  {
    description: t('mailclient::mail.account.featured.types'),
    icon: 'CheckCircle',
  },
]

async function destroy(id) {
  await Innoclapps.dialog().confirm({
    message: t('mailclient::mail.account.delete_warning'),
  })

  store
    .dispatch('emailAccounts/destroy', id)
    .then(() => Innoclapps.success(t('mailclient::mail.account.deleted')))
}

function createShared() {
  Innoclapps.dialog()
    .confirm({
      message: t('mailclient::mail.account.create_shared_confirmation_message'),
      title: false,
      icon: 'QuestionMarkCircle',
      iconWrapperColorClass: 'bg-info-100',
      iconColorClass: 'text-info-400',
      html: true,
      confirmText: t('core::app.continue'),
      confirmVariant: 'secondary',
    })
    .then(() =>
      router.push({
        name: 'create-email-account',
        query: {
          type: 'shared',
        },
      })
    )
}

/**
 * Handle create personal account route
 *
 * @return {Void}
 */
function createPersonal() {
  router.push({
    name: 'create-email-account',
    query: {
      type: 'personal',
    },
  })
}

function edit(account) {
  router.push({
    name: 'edit-email-account',
    params: { id: account.id },
  })
}

function togglePrimaryState(isPrimary, account) {
  if (isPrimary) {
    store.dispatch('emailAccounts/setPrimary', account.id)
  } else {
    store.dispatch('emailAccounts/removePrimary')
  }
}

function toggleDisabledSyncState(account) {
  let action = account.is_sync_disabled ? 'enable' : 'disable'
  store.dispatch(`emailAccounts/${action}Sync`, account.id)
}

function reAuthenticate(account) {
  if (account.connection_type === 'Imap') {
    edit(account)
  } else {
    window.location.href =
      store.getters['emailAccounts/OAuthConnectUrl'](
        account.connection_type,
        account.type
      ) + '?re_auth=1'
  }
}

// Used in EmailAccountForm.vue as well via the store
store.dispatch('emailAccounts/fetch').finally(() => {
  setLoading(false)
  if (route.query.viaOAuth) {
    edit(latestAccount.value)
  }
})
</script>
