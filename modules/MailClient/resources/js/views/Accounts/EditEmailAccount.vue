<template>
  <ISlideover
    @hidden="$router.back()"
    :title="$t('mailclient::mail.account.edit')"
    :visible="true"
    static-backdrop
    form
    @submit="update"
    @keydown="form.onKeydown($event)"
  >
    <IAlert
      v-if="isSyncDisabled"
      class="mb-4 border border-warning-200"
      variant="warning"
    >
      {{ account.sync_state_comment }}
    </IAlert>

    <EmailAccountFormFields
      :form="form"
      :test-connection-form="testConnectionForm"
      :type="account.type || ''"
      :account="account"
    />
    <template #modal-ok>
      <IButton
        v-if="isImapAccount && requirements.imap"
        :loading="testConnectionInProgress"
        :disabled="testConnectionInProgress"
        :text="
          $t('mailclient::mail.account.test_connection_and_retrieve_folders')
        "
        @click="performImapConnectionTest"
      />
      <IButton
        type="submit"
        variant="primary"
        :loading="form.busy"
        :text="$t('core::app.save')"
        :disabled="!isConnectionSuccessful || form.busy"
      />
    </template>
  </ISlideover>
</template>
<script setup>
import { ref, computed } from 'vue'
import EmailAccountFormFields from './EmailAccountFormFields.vue'
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useForm } from '~/Core/resources/js/composables/useForm'
import { useTestImapConnection } from '../../composables/useTestImapConnection'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const store = useStore()

const { testConnectionForm, testConnection, testConnectionInProgress } =
  useTestImapConnection()

const { form } = useForm()
const requirements = Innoclapps.config('requirements')
const account = ref({})

const isImapAccount = computed(() => form.connection_type === 'Imap')

const isSyncDisabled = computed(
  () => account.value.is_sync_stopped || account.value.is_sync_disabled
)

const isConnectionSuccessful = computed(
  () => store.state.emailAccounts.formConnectionState
)

function update() {
  store
    .dispatch('emailAccounts/update', {
      form: form,
      id: route.params.id,
    })
    .then(emailAccount => {
      Innoclapps.success(t('mailclient::mail.account.updated'))
      router.back()
    })
}

function performImapConnectionTest() {
  testConnection(form).then(data => {
    form.requires_auth = false
    form.folders = data.folders
  })
}

function prepareComponent() {
  store.commit('emailAccounts/SET_FORM_CONNECTION_STATE', true)

  store.dispatch('emailAccounts/get', route.params.id).then(emailAccount => {
    form.set(emailAccount)
    form.folders = emailAccount.folders_tree
    account.value = emailAccount
  })
}

prepareComponent()
</script>
