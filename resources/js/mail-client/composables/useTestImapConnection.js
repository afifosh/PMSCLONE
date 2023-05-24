import { ref } from 'vue'
import { useStore } from 'vuex'
import { useForm } from './Core/useForm'

export function useTestImapConnection() {
  const { form: testConnectionForm } = useForm()
  const testConnectionInProgress = ref(false)
  const store = useStore()

  function testConnection(form) {
    testConnectionForm.set({
      id: form.id || null,
      connection_type: form.connection_type,
      email: form.email,
      password: form.password,
      username: form.username,
      imap_server: form.imap_server,
      imap_port: form.imap_port,
      imap_encryption: form.imap_encryption,
      smtp_server: form.smtp_server,
      smtp_port: form.smtp_port,
      smtp_encryption: form.smtp_encryption,
      validate_cert: form.validate_cert,
    })

    testConnectionInProgress.value = true

    return new Promise((resolve, reject) => {
      testConnectionForm
        .post('/mail/accounts/connection')
        .then(data => {
          store.commit('emailAccounts/SET_FORM_CONNECTION_STATE', true)
          resolve(data)
        })
        .catch(error => {
          store.commit('emailAccounts/SET_FORM_CONNECTION_STATE', false)
          reject(error)
        })
        .finally(() => (testConnectionInProgress.value = false))
    })
  }

  return {
    testConnection,
    testConnectionInProgress,
    testConnectionForm,
  }
}
