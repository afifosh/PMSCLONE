<template>
  <IModal
    size="xl"
    id="replyMessageModal"
    static-backdrop
    @hide="handleModalHide"
    @hidden="handleModalHidden"
    @shown="handleModalShown"
    :hide-footer="showTemplates"
    :visible="visible"
    :title="
      $t(
        'mailclient::inbox.' +
          (forward ? 'forward_message' : 'reply_to_message'),
        {
          subject: message.subject,
        }
      )
    "
  >
    <div
      class="-mx-6 mb-4 border-y border-neutral-200 px-6 py-3 dark:border-neutral-700"
    >
      <div class="flex">
        <div class="mr-4">
          <a
            href="#"
            @click.prevent="showTemplates = true"
            v-show="!showTemplates"
            class="link text-sm font-medium"
            v-t="'mailclient.mail.templates.templates'"
          />
          <a
            href="#"
            class="link text-sm font-medium"
            v-show="showTemplates"
            @click.prevent="showTemplates = false"
            v-t="'mailclient.mail.compose'"
          />
        </div>
        <div v-show="!showTemplates" class="font-medium">
          <AssociationsPopover
            v-model="form.associations"
            :custom-selected-records="customAssociationsValue"
            :associated="message.associations"
          />
        </div>
      </div>
    </div>

    <div v-show="!showTemplates">
      <IAlert variant="danger" dismissible :show="hasInvalidAddresses">
        {{ $t('mailclient::mail.validation.invalid_recipients') }}
      </IAlert>
      <MailRecipient
        :form="form"
        type="to"
        ref="toRef"
        @recipient-removed="dissociateRemovedRecipients"
        @recipient-selected="handleRecipientSelectedEvent"
        :label="$t('mailclient::inbox.to')"
      >
        <template #after>
          <div class="-mt-2 ml-2 space-x-2">
            <a
              v-if="!wantsCc"
              href="#"
              @click.prevent="setWantsCC"
              v-t="'mailclient.inbox.cc'"
              class="link"
            />
            <a
              v-if="!wantsBcc"
              href="#"
              @click.prevent="setWantsBCC"
              v-t="'mailclient.inbox.bcc'"
              class="link"
            />
          </div>
        </template>
      </MailRecipient>
      <hr class="my-3 border-t border-neutral-200 dark:border-neutral-700" />
      <div v-if="wantsCc">
        <MailRecipient
          :form="form"
          type="cc"
          @recipient-removed="dissociateRemovedRecipients"
          @recipient-selected="associateSelectedRecipients"
          :label="$t('mailclient::inbox.cc')"
        />
        <hr class="my-3 border-t border-neutral-200 dark:border-neutral-700" />
      </div>
      <div v-if="wantsBcc">
        <MailRecipient
          :form="form"
          type="bcc"
          :label="$t('mailclient::inbox.bcc')"
        />
        <hr class="my-3 border-t border-neutral-200 dark:border-neutral-700" />
      </div>
      <div class="flex items-center">
        <div class="w-14">
          <IFormLabel for="subject" :label="$t('mailclient::inbox.subject')" />
        </div>
        <div class="grow">
          <IFormInput id="subject" v-model="form.subject" />
          <IFormError v-text="form.getError('subject')" />
        </div>
      </div>
      <hr class="my-3 border-t border-neutral-200 dark:border-neutral-700" />
      <Editor
        @placeholder-inserted="parsePlaceholdersForMessage"
        :placeholders="placeholders"
        ref="editorRef"
        :with-drop="true"
        v-model="form.message"
      />
      <div class="relative mt-3">
        <MediaUpload
          @file-uploaded="handleAttachmentUploaded"
          :action-url="`${$store.state.apiURL}/media/pending/${attachmentsDraftId}`"
          :select-file-text="$t('core::app.attach_files')"
        >
          <MediaItemsList
            :class="{
              'border-b border-neutral-200 dark:border-neutral-700':
                attachmentsBeingForwarded.length > 0 && attachments.length > 0,
            }"
            :items="attachmentsBeingForwarded"
            :authorize-delete="true"
            @delete-requested="removeAttachmentBeingForwarded"
          />
          <MediaItemsList
            class="mb-3"
            :items="attachments"
            :authorize-delete="true"
            @delete-requested="destroyPendingAttachment"
          />
        </MediaUpload>
      </div>
    </div>
    <template #modal-footer="{ cancel }">
      <div class="flex flex-col sm:flex-row sm:items-center">
        <div class="grow">
          <CreateFollowUpTask :form="form" v-show="Boolean(resourceName)" />
        </div>
        <div
          class="mt-2 space-y-2 sm:mt-0 sm:flex sm:items-center sm:space-x-2 sm:space-y-0"
        >
          <IButton
            class="w-full sm:w-auto"
            variant="white"
            @click="cancel"
            :text="$t('core::app.cancel')"
          />
          <IButton
            class="w-full sm:w-auto"
            :loading="sending"
            :disabled="sendButtonIsDisabled"
            :text="
              !forward
                ? $t('mailclient::inbox.reply')
                : $t('mailclient::inbox.forward')
            "
            @click="send"
          />
        </div>
      </div>
    </template>
    <MailTemplates v-if="showTemplates" @selected="handleTemplateSelected" />
  </IModal>
</template>
<script setup>
import { ref, computed, nextTick } from 'vue'
import CreateFollowUpTask from '~/Activities/resources/js/components/CreateFollowUpTask.vue'
import MailRecipient from './RecipientSelectorField.vue'
import Editor from '../../components/MailEditor'
import AssociationsPopover from '~/Core/resources/js/components/AssociationsPopover.vue'
import MediaUpload from '~/Core/resources/js/components/Media/MediaUpload.vue'
import MediaItemsList from '~/Core/resources/js/components/Media/MediaItemsList.vue'
import MailTemplates from '../Templates/MailTemplateList.vue'
import findIndex from 'lodash/findIndex'
import cloneDeep from 'lodash/cloneDeep'
import { randomString } from '@/utils'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useMessageComposer } from '../../composables/useMessageComposer'
import { useI18n } from 'vue-i18n'
import { useSignature } from './useSignature'

const cleanSubjectSearch = [
  // Re
  'RE:',
  'SV:',
  'Antw:',
  'VS:',
  'RE:',
  'REF:',
  'ΑΠ:',
  'ΣΧΕΤ:',
  'Vá:',
  'R:',
  'RIF:',
  'BLS:',
  'RES:',
  'Odp:',
  'YNT:',
  'ATB:',
  // FW
  'FW:',
  'FWD:',
  'Doorst:',
  'VL:',
  'TR:',
  'WG:',
  'ΠΡΘ:',
  'Továbbítás:',
  'I:',
  'FS:',
  'TRS:',
  'VB:',
  'RV:',
  'ENC:',
  'PD:',
  'İLT:',
  'YML:',
]

const emit = defineEmits(['modal-hidden'])

const props = defineProps({
  resourceName: String,
  resourceRecord: Object, // Needs to be provided if resourceName is provided
  visible: { type: Boolean, default: false },
  toAll: { type: Boolean, default: false },
  forward: { type: Boolean, default: false },
  message: { type: Object, required: true },
})

const { t } = useI18n()

const { dateFromAppTimezone } = useDates()
const { addSignature } = useSignature()

const {
  form,
  attachments,
  attachmentsDraftId,
  handleAttachmentUploaded,
  destroyPendingAttachment,
  customAssociationsValue,
  placeholders,
  parsePlaceholdersForMessage,
  sending,
  sendRequest,
  hasInvalidAddresses,
  associateSelectedRecipients,
  dissociateRemovedRecipients,
  handleRecipientSelectedEvent,
  setWantsCC,
  setWantsBCC,
  wantsBcc,
  wantsCc,
} = useMessageComposer(props.resourceName, props.resourceRecord)

form.set('associations', props.message.associations)

const toRef = ref(null)
const editorRef = ref(null)

const showTemplates = ref(false)
const attachmentsBeingForwarded = ref([])

const sendButtonIsDisabled = computed(
  () => form.to.length === 0 || sending.value
)

const hasCC = computed(() => props.message.cc && props.message.cc.length > 0)

const hasReplyTo = computed(
  () => props.message.reply_to && props.message.reply_to.length > 0
)

function removeAttachmentBeingForwarded(media) {
  const index = findIndex(attachmentsBeingForwarded.value, ['id', media.id])
  attachmentsBeingForwarded.value.splice(index, 1)
}

function handleTemplateSelected(template) {
  form.message = template.body + form.message
  showTemplates.value = false
  parsePlaceholdersForMessage()
  nextTick(() => editorRef.value.focus())
}

function handleModalShown() {
  attachmentsDraftId.value = randomString()
  let subject = cleanupSubject(props.message.subject)

  if (props.forward) {
    attachmentsBeingForwarded.value = cloneDeep(props.message.media)

    // Reset the recipients on forward
    ;['cc', 'bcc', 'to'].forEach(key => form.set(key, key === 'to' ? [] : null))

    form.message =
      "<br /><div class='concord_attr'>" +
      t('mailclient::inbox.forwarded_message_placeholder', {
        from: `${
          props.message.from.name ? props.message.from.name + ' ' : ''
        }&lt;${props.message.from.address}&gt;`,
        date: dateFromAppTimezone(props.message.date, 'llll'),
        subject: props.message.subject,
        to: props.message.to
          .reduce((carry, to) => {
            carry.push((to.name ? to.name + ' ' : '') + `&lt;${to.address}&gt;`)
            return carry
          }, [])
          .join(', '),
        pre: '----------',
        after: '---------',
      }) +
      '</div>' +
      '<br /><div>' +
      props.message.editor_text +
      '</div>'

    if (subject) {
      subject = Innoclapps.config('mail.forward_prefix') + subject
    }

    toRef.value.focus()
  } else {
    attachmentsBeingForwarded.value = []
    setRecipients()
    form.message = createQuoteOfPreviousMessage()

    setTimeout(() => editorRef.value.focus(), 800)

    if (subject) {
      subject = Innoclapps.config('mail.reply_prefix') + subject
    }
  }

  form.message = addSignature(form.message)

  form.subject = subject
}

/**
 * Create quote from the message
 *
 * @return {String}
 */
function createQuoteOfPreviousMessage() {
  let body = props.message.editor_text

  // Maybe the message was empty?
  if (!body) {
    return ''
  }

  let from = `&lt;${props.message.from.address}&gt;`

  if (props.message.from.name) {
    from = props.message.from.name + ' ' + from
  }

  let wroteText = `On ${dateFromAppTimezone(
    props.message.date,
    'llll'
  )} ${from} wrote:`

  // 2 new lines allow the EmailReplyParser to properly determine the actual reply message
  return (
    "<br /><div class='concord_attr'>" +
    wroteText +
    '</div><blockquote class="concord_quote">' +
    body +
    '</blockquote>'
  )
}

/**
 * Handle modal hide event
 *
 * Reset the state, we need to reset the form and the
 * attachments because when the modal is hidden each time
 * new attachmentsDraftId is generated
 *
 * @return {Void}
 */
function handleModalHide() {
  form.reset()
  attachments.value = []
  customAssociationsValue.value = {}
}

function handleModalHidden() {
  emit('modal-hidden')
}

/**
 * Send the message
 *
 * @return {Void}
 */
function send() {
  if (!props.forward) {
    sendRequest(`/emails/${props.message.id}/reply`).then(() =>
      Innoclapps.modal().hide('replyMessageModal')
    )
  } else {
    form.fill(
      'forward_attachments',
      attachmentsBeingForwarded.value.map(attach => attach.id)
    )
    sendRequest(`/emails/${props.message.id}/forward`).then(() =>
      Innoclapps.modal().hide('replyMessageModal')
    )
  }
}

/**
 * Set reply to all
 */
function setReplyToAll() {
  if (!setRecipientsViaReplyToHeader()) {
    setRecipientsViaFromHeader()
  }

  if (hasCC.value) {
    let cc = []
    props.message.cc.forEach((recipient, index) => {
      let existsAsReplyTo = findIndex(props.message.reply_to, [
        'address',
        recipient.address,
      ])

      if (existsAsReplyTo === -1) {
        cc.push({
          address: recipient.address,
          name: recipient.name,
        })
      }
    })
    if (cc.length > 0) {
      form.set('cc', cc)
    }
  }
}

/**
 * Set the toa via reply to header
 */
function setRecipientsViaReplyToHeader() {
  if (hasReplyTo.value) {
    let recipients = []

    props.message.reply_to.forEach(recipient => {
      recipients.push({
        address: recipient.address,
        name: recipient.name,
      })
    })

    form.set('to', recipients)

    return true
  }

  return false
}

function setRecipientsViaToHeader() {
  form.set(
    'to',
    props.message.to.map(to => ({
      address: to.address,
      name: to.name,
    }))
  )
}

/**
 * Set the toa via the from header
 */
function setRecipientsViaFromHeader() {
  if (props.message.from) {
    // Maybe draft with no from header?
    form.set('to', [
      {
        address: props.message.from.address,
        name: props.message.from.name,
      },
    ])
  }
}

function setRecipients() {
  if (props.toAll) {
    setReplyToAll()
  } else {
    // Replying to message sent via account e.q. via sent folder,
    // in this case, we will set the TO header via the actual recipient
    // e.q. sent a message, go to inbox, hit reply, it will use the original recipient
    // email not the account email
    if (
      (props.message.reply_to.length === 1 &&
        props.message.reply_to[0].address ===
          props.message.email_account_email) ||
      (!hasReplyTo.value &&
        props.message.from.address === props.message.email_account_email)
    ) {
      setRecipientsViaToHeader()
    } else if (!setRecipientsViaReplyToHeader()) {
      setRecipientsViaFromHeader()
    }

    // Reset the CC in case of previous replyToAll clicked
    form.set('cc', null)
  }
}

function cleanupSubject(subject) {
  if (subject === null) {
    return subject
  }

  const search = cleanSubjectSearch

  search.push(
    ...[
      Innoclapps.config('mail.reply_prefix'),
      Innoclapps.config('mail.forward_prefix'),
    ]
  )

  return subject.replace(new RegExp(search.join('|'), 'gmi'), '').trim()
}
</script>
