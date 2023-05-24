<template>
  <IAlert variant="info" class="mb-5" :show="accountConfigError !== null">
    <div v-html="accountConfigError" />
  </IAlert>
  <div
    :class="{
      'mb-3 rounded-md border border-warning-400 px-4 py-3':
        !form.connection_type,
    }"
  >
    <IFormGroup
      required
      :label="$t('mailclient::mail.account.type')"
      label-for="connection_type"
    >
      <ICustomSelect
        :options="accountTypes"
        :clearable="false"
        :placeholder="$t('mailclient::mail.account.select_type')"
        :disabled="!isCreateView"
        @option:selected="handleAccountConnectionTypeChange"
        v-model="form.connection_type"
      >
      </ICustomSelect>
      <IFormError v-text="form.getError('connection_type')" />
    </IFormGroup>
    <div
      v-if="isCreateView && hasHangingOAuthAccounts"
      :class="{ 'mb-3': form.connection_type }"
    >
      <p
        class="mb-2 mt-4 text-sm text-neutral-800 dark:text-neutral-200"
        v-t="'core::oauth.or_choose_existing'"
      />
      <OAuthAccount
        v-for="oAuthAccount in notConnectedOAuthAccounts"
        :key="oAuthAccount.id"
        :account="oAuthAccount"
        :with-reconnect-link="false"
        class="mb-2"
      >
        <IButton
          class="ml-2"
          size="sm"
          @click="connectExistingOAuthAccount(oAuthAccount)"
          :disabled="oAuthAccount.requires_auth"
          :text="$t('core::oauth.connect')"
        />
      </OAuthAccount>
    </div>
  </div>
  <div
    v-if="isCreateView"
    class="mb-3 rounded-md border border-neutral-200 p-3 dark:border-neutral-600"
  >
    <IFormLabel :label="$t('mailclient::mail.account.sync_emails_from')" />
    <div class="mt-3 flex flex-col items-center sm:flex-row sm:space-x-2">
      <IFormRadio
        v-for="initialSync in initialSyncOptions"
        :key="initialSync.value"
        :label="initialSync.text"
        class="self-start"
        :id="'initial-sync-' + initialSync.value"
        :value="initialSync.value"
        v-model="form.initial_sync_from"
        name="initial_sync_from"
      />
    </div>
    <IAlert v-if="showInitialSyncOptionWarning" class="mt-4">
      {{
        $t('mailclient::mail.account.sync_period_note', {
          date: localizedDate(form.initial_sync_from),
        })
      }}
    </IAlert>
  </div>
  <div
    :class="{
      'pointer-events-none blur-sm': shouldBlurServerConfigureableFields,
    }"
  >
    <IFormGroup
      :label="$t('mailclient::mail.account.email_address')"
      label-for="email"
      required
    >
      <IFormInput
        v-model="form.email"
        name="email"
        :disabled="!isCreateView"
        spellcheck="false"
        autocomplete="off"
        type="email"
      >
      </IFormInput>
      <IFormError v-text="form.getError('email')" />
    </IFormGroup>
    <IFormGroup>
      <IFormCheckbox
        id="create_contact"
        name="create_contact"
        v-model:checked="form.create_contact"
        :label="$t('mailclient::mail.account.create_contact')"
      />
    </IFormGroup>
  </div>
  <div
    :class="{
      'pointer-events-none blur-sm': shouldBlurServerConfigureableFields,
    }"
    v-show="shouldShowServerConfigureableFields || isCreateView"
  >
    <IFormGroup
      :label="$t('mailclient::mail.account.password')"
      label-for="password"
      required
    >
      <IFormInput
        v-model="form.password"
        :placeholder="form.id ? '•••••••••••' : ''"
        spellcheck="false"
        autocomplete="new-password"
        type="password"
      >
      </IFormInput>
      <IFormError v-text="form.getError('password')" />
    </IFormGroup>
    <IFormGroup
      label-for="username"
      :label="$t('mailclient::mail.account.username')"
      optional
    >
      <IFormInput
        v-model="form.username"
        autocomplete="off"
        spellcheck="false"
        name="username"
        id="username"
      >
      </IFormInput>
    </IFormGroup>
    <div class="mb-3 mt-4">
      <h5
        class="mb-3 font-medium text-neutral-700 dark:text-neutral-100"
        v-t="'mailclient.mail.account.incoming_mail'"
      />
      <IFormGroup
        required
        :label="$t('mailclient::mail.account.server')"
        label-for="imap_server"
      >
        <IFormInput
          v-model="form.imap_server"
          name="imap_server"
          placeholder="imap.example.com"
          spellcheck="false"
          autocomplete="off"
        >
        </IFormInput>
        <IFormError v-text="form.getError('imap_server')" />
      </IFormGroup>
      <div class="grid grid-cols-6 gap-6">
        <div class="col-span-2">
          <IFormGroup
            :label="$t('mailclient::mail.account.port')"
            label-for="imap_port"
            required
          >
            <IFormInput
              v-model="form.imap_port"
              name="imap_port"
              type="number"
              autocomplete="off"
            >
            </IFormInput>
            <IFormError v-text="form.getError('imap_port')" />
          </IFormGroup>
        </div>
        <div class="col-span-4">
          <IFormGroup
            :label="$t('mailclient::mail.account.encryption')"
            label-for="imap_encryption"
          >
            <ICustomSelect
              :options="encryptions"
              :placeholder="$t('mailclient::mail.account.without_encryption')"
              v-model="form.imap_encryption"
            />
            <IFormError v-text="form.getError('imap_encryption')" />
          </IFormGroup>
        </div>
      </div>
    </div>
    <h5
      class="mb-3 font-medium text-neutral-700 dark:text-neutral-100"
      v-t="'mailclient.mail.account.outgoing_mail'"
    />
    <IFormGroup
      required
      :label="$t('mailclient::mail.account.server')"
      label-for="smtp_server"
    >
      <IFormInput
        v-model="form.smtp_server"
        name="smtp_server"
        placeholder="smtp.example.com"
        spellcheck="false"
        autocomplete="off"
      >
      </IFormInput>
      <IFormError v-text="form.getError('smtp_server')" />
    </IFormGroup>
    <div class="grid grid-cols-6 gap-6">
      <div class="col-span-2">
        <IFormGroup
          :label="$t('mailclient::mail.account.port')"
          label-for="smtp_port"
          required
        >
          <IFormInput
            v-model="form.smtp_port"
            name="smtp_port"
            type="number"
            autocomplete="off"
          >
          </IFormInput>
          <IFormError v-text="form.getError('smtp_port')" />
        </IFormGroup>
      </div>
      <div class="col-span-4">
        <IFormGroup
          :label="$t('mailclient::mail.account.encryption')"
          label-for="smtp_encryption"
        >
          <ICustomSelect
            :options="encryptions"
            :placeholder="$t('mailclient::mail.account.without_encryption')"
            v-model="form.smtp_encryption"
          />
          <IFormError v-text="form.getError('smtp_encryption')" />
        </IFormGroup>
      </div>
    </div>
    <IFormGroup>
      <IFormCheckbox
        id="validate_cert"
        name="validate_cert"
        v-model:checked="form.validate_cert"
        :label="$t('mailclient::mail.account.allow_non_secure_certificate')"
        :value="0"
        :unchecked-value="1"
      />
      <IFormError v-text="form.getError('validate_cert')" />
    </IFormGroup>
  </div>
  <!-- Outlook account from custom header not working -->
  <div
    :class="{
      'pointer-events-none blur-sm': shouldBlurServerConfigureableFields,
      hidden: form.connection_type === 'Outlook',
    }"
  >
    <h5
      class="mb-3 font-medium text-neutral-700 dark:text-neutral-100"
      v-t="'mailclient.mail.from_header'"
    />
    <div
      class="mb-3 rounded-md border border-neutral-200 p-3 dark:border-neutral-600"
    >
      <IFormGroup
        :label="$t('mailclient::mail.from_name')"
        :description="
          $t('mailclient::mail.placeholders_info', {
            placeholders: '{agent}, {company}',
          })
        "
      >
        <IFormInput
          v-model="form.from_name_header"
          name="from_name_header"
          autocomplete="off"
        >
        </IFormInput>
        <IFormError v-text="form.getError('from_name_header')" />
      </IFormGroup>
      <IFormGroup>
        <p
          class="mb-1 font-medium text-neutral-700 dark:text-neutral-100"
          v-t="'core::app.preview'"
        />
        <p
          class="mb-2 text-sm text-neutral-700 dark:text-neutral-300"
          v-t="'mailclient.mail.from_header_info'"
        />
        <div
          class="rounded-md border border-neutral-200 p-3 dark:border-neutral-600"
        >
          <div class="flex items-center">
            <div class="mr-4">
              <Icon
                icon="Mail"
                class="h-6 w-6 text-neutral-600 dark:text-neutral-300"
              />
            </div>
            <div>
              <h6
                class="font-medium text-neutral-800 dark:text-neutral-100"
                v-text="parsedFromNameHeader"
              />
              <p
                class="text-neutral-700 dark:text-neutral-300"
                v-show="form.email"
                v-text="'<' + form.email + '>'"
              />
            </div>
          </div>
        </div>
      </IFormGroup>
    </div>
  </div>
  <div
    :class="{
      'pointer-events-none blur-sm': shouldBlurServerConfigureableFields,
    }"
    v-show="shouldShowServerConfigureableFields"
  >
    <IFormGroup
      v-if="testConnectionForm.errors && testConnectionForm.errors.any()"
    >
      <IAlert variant="danger" class="mt-3" show>
        <p
          v-for="(error, field) in testConnectionForm.errors.all()"
          :key="field"
          class="mb-1"
          v-text="testConnectionForm.getError(field)"
        />
      </IAlert>
    </IFormGroup>
  </div>
  <div v-if="account">
    <FolderTypeSelect
      v-model="form.sent_folder_id"
      :form="form"
      :folders="account.folders"
      field="sent_folder_id"
      :required="true"
      :label="$t('mailclient::mail.account.select_sent_folder')"
    />
    <FolderTypeSelect
      v-model="form.trash_folder_id"
      :form="form"
      :required="true"
      :folders="account.folders"
      field="trash_folder_id"
      :label="$t('mailclient::mail.account.select_trash_folder')"
    />
  </div>
  <IFormGroup
    v-if="foldersFetched"
    :label="$t('mailclient::mail.account.active_folders')"
  >
    <FormFolders class="mt-3" :folders="form.folders" />
  </IFormGroup>
</template>
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import find from 'lodash/find'
import reject from 'lodash/reject'
import FolderTypeSelect from './EmailAccountFormFolderTypeSelect.vue'
import FormFolders from './EmailAccountFormFolders.vue'
import OAuthAccount from '../Core/OAuth/OAuthAccount.vue'
import { useStore } from 'vuex'
import { useDates } from '../../composables/useDates'
import { useI18n } from 'vue-i18n'
import { useApp } from '../../composables/useApp'

const emit = defineEmits(['submit', 'ready'])

const props = defineProps({
  type: { required: true, type: String },
  account: Object,
  form: {
    required: true,
    type: Object,
    default() {
      return {}
    },
  },
  testConnectionForm: {
    required: true,
    type: Object,
    default() {
      return {}
    },
  },
})

const { t } = useI18n()
const store = useStore()

const {
  currentUser,
  setting,
  isMicrosoftGraphConfigured,
  isGoogleApiConfigured,
} = useApp()

const { localizedDate, appDate, appMoment } = useDates()

const initialSyncOptions = [
  {
    text: t('mailclient::mail.account.sync_period_now'),
    value: appDate(),
  },
  {
    text: t('mailclient::mail.account.sync_period_1_month_ago'),
    value: createPeriodDate(1),
  },
  {
    text: t('mailclient::mail.account.sync_period_3_months_ago'),
    value: createPeriodDate(3),
  },
  {
    text: t('mailclient::mail.account.sync_period_6_months_ago'),
    value: createPeriodDate(6),
    warning: true,
  },
]

const encryptions = Innoclapps.config('mail.accounts.encryptions')
const accountTypes = Innoclapps.config('mail.accounts.connections')

const connectedUserOAuthAccounts = ref([])

const accounts = computed(() => store.getters['emailAccounts/accounts'])

/**
 * Get all the user not connected OAuth accounts to email account
 */
const notConnectedOAuthAccounts = computed(() =>
  reject(connectedUserOAuthAccounts.value, account =>
    find(accounts.value, ['email', account.email])
  )
)

/**
 * Check whether the user has OAuth accounts that are not connected as email account
 */
const hasHangingOAuthAccounts = computed(
  () => notConnectedOAuthAccounts.value.length > 0
)

const showInitialSyncOptionWarning = computed(() => {
  let option = find(initialSyncOptions, ['value', props.form.initial_sync_from])

  if (option) {
    return option.warning === true
  }
  return false
})

/**
 * Get the account config error
 */
const accountConfigError = computed(
  () => store.state.emailAccounts.accountConfigError
)

/**
 * Get the FROM NAME header for the preview
 */
const parsedFromNameHeader = computed(() => {
  if (!props.form.from_name_header) {
    return ''
  }

  return props.form.from_name_header
    .replace('{agent}', currentUser.value.name)
    .replace('{company}', setting('company_name') || '')
})

/**
 * Check whether the form is for create
 */
const isCreateView = computed(() => !Boolean(props.account))

/**
 * Check whether the selected acount is IMAP
 */
const isImapAccount = computed(() => props.form.connection_type === 'Imap')

/**
 * Check whether the server configurable fields should be blurred
 */
const shouldBlurServerConfigureableFields = computed(
  () => isCreateView.value && !isImapAccount.value
)

/**
 * Check whether the server configurable fields should be hidden
 */
const shouldShowServerConfigureableFields = computed(() => isImapAccount.value)

/**
 * Check whether the IMAP account folders are fetched
 */
const foldersFetched = computed(() => {
  if (!props.form.folders) {
    return false
  }

  return props.form.folders.length > 0
})

function setConnectionSuccessful(bool) {
  return store.commit('emailAccounts/SET_FORM_CONNECTION_STATE', bool)
}

function setAccountConfigError(error) {
  return store.commit('emailAccounts/SET_ACCOUNT_CONFIG_ERROR', error)
}

/**
 * Create period date for the option for initial sync
 *
 * @param  {Number} months
 *
 * @return {String}
 */
function createPeriodDate(months) {
  return appMoment().subtract(months, 'months').format('YYYY-MM-DD HH:mm:ss')
}

/**
 * Handle account connection type changes
 *
 * @param  {String} val
 *
 * @return {Void}
 */
function handleAccountConnectionTypeChange(val) {
  setAccountConfigError(null)
  if (val == 'Outlook' && !isMicrosoftGraphConfigured()) {
    setAccountConfigError(`Microsoft application not configured,
                        you must <a href="/settings/integrations/microsoft" rel="noopener noreferrer" target="_blank" class="font-medium underline text-danger-700 hover:text-danger-600 focus:outline-none">configure</a> your
                        Microsoft application in order to connect Outlook mail client.`)
  } else if (val == 'Gmail' && !isGoogleApiConfigured()) {
    setAccountConfigError(`Google application project not configured,
                        you must <a href="/settings/integrations/google" rel="noopener noreferrer" target="_blank" class="font-medium underline text-danger-700 hover:text-danger-600 focus:outline-none">configure</a> your
                        Google application project in order to connect Gmail mail client.`)
  } else if (val === 'Imap' && !Innoclapps.config('requirements.imap')) {
    setAccountConfigError(
      `In order to use IMAP account type, you will need to enable the PHP extension "imap".`
    )
  }
}

/**
 * Retrieve the oAuth accounts for the user
 *
 * @return {Void}
 */
function retrieveUserConnectedOAuthAccounts() {
  Innoclapps.request()
    .get('oauth/accounts')
    .then(({ data }) => (connectedUserOAuthAccounts.value = data))
}

/**
 * Connect the existing OAuth account
 *
 * @param  {Object} account
 *
 * @return {Void}
 */
function connectExistingOAuthAccount(account) {
  switch (account.type) {
    case 'microsoft':
      props.form.fill('connection_type', 'Outlook')
      break
    case 'google':
      props.form.fill('connection_type', 'Gmail')
      break
    default:
      props.form.fill('connection_type', account.type)
  }

  emit('submit')
}

onMounted(() => {
  retrieveUserConnectedOAuthAccounts()
  emit('ready')
})

onUnmounted(() => {
  // Reset connection state
  setConnectionSuccessful(false)
  setAccountConfigError(null)
})

defineExpose({ initialSyncOptions })
</script>
