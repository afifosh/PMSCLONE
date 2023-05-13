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
import { ref, unref, computed, onBeforeMount } from 'vue'
import findIndex from 'lodash/findIndex'
import find from 'lodash/find'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useI18n } from 'vue-i18n'
import { randomString } from '@/utils'
import { useStore } from 'vuex'
import { useForm } from '~/Core/resources/js/composables/useForm'
import { useSignature } from '../views/Emails/useSignature'

export function useMessageComposer(viaResource, resourceRecord) {
  const { t } = useI18n()
  const store = useStore()
  const { addSignature } = useSignature()
  const { addResourceRecordHasManyRelationship, incrementResourceRecordCount } =
    useRecordStore()

  const sending = ref(false)
  const customAssociationsValue = ref({})
  const attachmentsDraftId = ref(randomString())
  const attachments = ref([])

  const { form } = useForm({
    with_task: false,
    task_date: null,
    subject: null,
    message: addSignature(),
    to: [],
    cc: null,
    bcc: null,
    associations: {},
  })

  const placeholders = computed(() => store.state.fields.placeholders)

  const hasInvalidAddresses = computed(() => {
    return Boolean(
      form.errors.first('to.0.address') ||
        form.errors.first('cc.0.address') ||
        form.errors.first('bcc.0.address')
    )
  })

  const wantsCc = computed(() => form.cc !== null)
  const wantsBcc = computed(() => form.bcc !== null)

  function parsePlaceholdersForMessage() {
    if (!form.message) {
      return
    }

    const resources = []

    if (form.to.length > 0 && form.to[0].resourceName) {
      resources.push({
        name: form.to[0].resourceName,
        id: form.to[0].id,
      })
    }

    // viaResource
    if (viaResource) {
      resources.push({
        name: viaResource,
        id: unref(resourceRecord).id,
      })
    }

    if (resources.length > 0) {
      parsePlaceholders(resources, form.message).then(
        content => (form.message = content)
      )
    }
  }

  function handleCreatedFollowUpTask(task) {
    addResourceRecordHasManyRelationship(task, 'activities')
    incrementResourceRecordCount('incomplete_activities_for_user_count')
  }

  function sendRequest(url) {
    sending.value = true
    form.fill('attachments_draft_id', attachmentsDraftId)

    if (viaResource) {
      form.fill('via_resource', viaResource)
      form.fill('via_resource_id', unref(resourceRecord).id)
    }

    return Innoclapps.request()
      .post(url, form.data())
      .then(response => {
        form.reset()

        if (response.status !== 202) {
          Innoclapps.success(t('mailclient::inbox.message_sent'))
          Innoclapps.$emit('email-sent', response.data.message)
        } else {
          Innoclapps.info(t('mailclient::mail.message_queued_for_sending'))
        }

        if (response.data.createdActivity && viaResource) {
          handleCreatedFollowUpTask(response.data.createdActivity)
        }
      })
      .finally(() => (sending.value = false))
  }

  function setWantsCC() {
    form.cc = []
  }

  function setWantsBCC() {
    form.bcc = []
  }

  function handleAttachmentUploaded(media) {
    attachments.value.push(media)
  }

  function destroyPendingAttachment(media) {
    Innoclapps.request()
      .delete(`/media/pending/${media.pending_data.id}`)
      .then(() => {
        let index = findIndex(attachments.value, ['id', media.id])
        attachments.value.splice(index, 1)
      })
  }

  function handleRecipientSelectedEvent(recipients) {
    associateSelectedRecipients(recipients)
    parsePlaceholdersForMessage()
  }

  /**
   * When a recipient is removed we will dissociate
   * the removed recipients from the associations component
   *
   * @param  {Object} option
   *
   * @return {Void}
   */
  function dissociateRemovedRecipients(option) {
    if (
      !option.resourceName ||
      !customAssociationsValue.value[option.resourceName]
    ) {
      return
    }

    let index = findIndex(customAssociationsValue.value[option.resourceName], [
      'id',
      option.id,
    ])

    if (index !== -1) {
      customAssociationsValue.value[option.resourceName].splice(index, 1)
    }
  }

  /**
   * When a recipient is selected we will associate automatically to the associatiosn component
   *
   * @param  {Array} records
   *
   * @return {Void}
   */
  function associateSelectedRecipients(records) {
    records.forEach(record => {
      if (record.resourceName) {
        if (!customAssociationsValue.value[record.resourceName]) {
          customAssociationsValue.value[record.resourceName] = []
        }

        if (
          !find(customAssociationsValue.value[record.resourceName], [
            'id',
            record.id,
          ])
        ) {
          customAssociationsValue.value[record.resourceName].push({
            id: record.id,
            display_name: record.name,
          })
        }
      }
    })
  }

  async function parsePlaceholders(resources, content) {
    if (!content) {
      return content
    }

    let { data } = await Innoclapps.request().post('/placeholders', {
      resources: resources,
      content: content,
    })

    return data
  }

  onBeforeMount(() => {
    store.dispatch('fields/fetchPlaceholders')
  })

  return {
    form,
    sending,
    customAssociationsValue,
    attachments,
    attachmentsDraftId,

    placeholders,
    hasInvalidAddresses,
    wantsCc,
    wantsBcc,

    sendRequest,
    parsePlaceholdersForMessage,
    handleAttachmentUploaded,
    destroyPendingAttachment,
    associateSelectedRecipients,
    dissociateRemovedRecipients,
    handleRecipientSelectedEvent,
    setWantsBCC,
    setWantsCC,
  }
}
