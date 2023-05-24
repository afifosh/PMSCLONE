<template>
  <IModal
    size="xl"
    id="composeMessageModal"
    static-backdrop
    @hidden="modalHidden"
    @shown="handleModalShown"
    :hide-footer="showTemplates"
    :visible="visible"
    :title="$t('mailclient::inbox.new_message')"
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
            :resource-name="resourceName"
            :associateable="resourceName ? resourceRecord : {}"
            :custom-selected-records="customAssociationsValue"
            v-model="form.associations"
          />
        </div>
      </div>
    </div>
    <div v-show="!showTemplates">
      <IOverlay :show="!componentReady">
        <IAlert variant="danger" dismissible :show="hasInvalidAddresses">
          {{ $t('mailclient::mail.validation.invalid_recipients') }}
        </IAlert>
        <MailRecipient
          :form="form"
          type="to"
          @recipient-removed="dissociateRemovedRecipients"
          @recipient-selected="handleRecipientSelectedEvent"
          :label="$t('mailclient::inbox.to')"
        >
          <template #after>
            <div class="-mt-2 ml-2 space-x-2" v-if="!wantsBcc || !wantsCc">
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
                class="link"
                v-t="'mailclient.inbox.bcc'"
              />
            </div>
          </template>
        </MailRecipient>
        <hr class="my-3 border-t border-neutral-200 dark:border-neutral-700" />
        <div v-show="wantsCc">
          <MailRecipient
            :form="form"
            type="cc"
            @recipient-removed="dissociateRemovedRecipients"
            @recipient-selected="associateSelectedRecipients"
            :label="$t('mailclient::inbox.cc')"
          />
          <hr
            class="my-3 border-t border-neutral-200 dark:border-neutral-700"
          />
        </div>
        <div v-show="wantsBcc">
          <MailRecipient
            :form="form"
            type="bcc"
            :label="$t('mailclient::inbox.bcc')"
          />
          <hr
            class="my-3 border-t border-neutral-200 dark:border-neutral-700"
          />
        </div>
        <div class="flex items-center">
          <IFormLabel :label="$t('mailclient::inbox.from')" class="w-14" />
          <FormDropdownSelect
            adaptive-width
            :items="accounts"
            value-key="id"
            v-model="account"
            label-key="email"
          >
            <template #label="{ label }">
              {{ account.formatted_from_name_header }}
              <span v-text="'<' + label + '>'"></span>
            </template>
          </FormDropdownSelect>
        </div>
        <hr class="my-3 border-t border-neutral-200 dark:border-neutral-700" />
        <div class="flex items-center">
          <div class="w-14">
            <IFormLabel
              :label="$t('mailclient::inbox.subject')"
              for="subject"
            />
          </div>
          <div class="grow">
            <IFormInput id="subject" v-model="form.subject" ref="subjectRef" />
            <IFormError v-text="form.getError('subject')" />
          </div>
        </div>
        <hr class="my-3 border-t border-neutral-200 dark:border-neutral-700" />
        <Editor
          v-model="form.message"
          :placeholders="placeholders"
          :with-drop="true"
          @placeholder-inserted="parsePlaceholdersForMessage"
          ref="editorRef"
        />
        <div class="relative mt-3">
          <MediaUpload
            @file-uploaded="handleAttachmentUploaded"
            :action-url="`${$store.state.apiURL}/media/pending/${attachmentsDraftId}`"
            :select-file-text="$t('core::app.attach_files')"
          >
            <MediaItemsList
              class="mb-3"
              :items="attachments"
              :authorize-delete="true"
              @delete-requested="destroyPendingAttachment"
            />
          </MediaUpload>
        </div>
      </IOverlay>
    </div>
    <template #modal-footer="{ cancel }">
      <div class="flex flex-col sm:flex-row sm:items-center">
        <div class="grow">
          <!-- <CreateFollowUpTask :form="form" v-show="Boolean(resourceName)" /> -->
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
            :text="$t('mailclient::inbox.send')"
            @click="send"
          />
        </div>
      </div>
    </template>
    <MailTemplates v-if="showTemplates" @selected="handleTemplateSelected" />
  </IModal>
</template>
<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useStore } from 'vuex'
// import CreateFollowUpTask from '~/Activities/resources/js/components/CreateFollowUpTask.vue'
import MailRecipient from './RecipientSelectorField.vue'
import Editor from '../../components/MailEditor'
import AssociationsPopover from '../../components/Core/AssociationsPopover.vue'
import MediaUpload from '../../components/Core/Media/MediaUpload.vue'
import MediaItemsList from '../../components/Core/Media/MediaItemsList.vue'
import MailTemplates from '../Templates/MailTemplateList.vue'
import { useMessageComposer } from '../../composables/useMessageComposer'
import { randomString } from '@/utils'

const emit = defineEmits(['modal-hidden'])

const props = defineProps({
  resourceName: String,
  resourceRecord: Object, // Needs to be provided if resourceName is provided
  visible: { type: Boolean, default: false },
  defaultAccount: Object,
  to: { type: Array, default: () => [] },
})

const store = useStore()

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

const editorRef = ref(null)
const subjectRef = ref(null)

const account = ref({})
const componentReady = ref(false)
const showTemplates = ref(false)

const accounts = computed(() => store.getters['emailAccounts/accounts'])

const sendButtonIsDisabled = computed(
  () => form.to.length === 0 || !form.subject || sending.value
)

watch(
  () => props.defaultAccount,
  newVal => {
    account.value = newVal
  }
)

// In case the to is updated
// we need to update the form value too
// e.q. update contact email and click create email
// if we don't update the value the old email will be used
watch(
  () => props.to,
  newVal => {
    form.to = newVal
    associateSelectedRecipients(newVal)
  },
  { immediate: true }
)

function handleTemplateSelected(template) {
  // Allow the sales agent to enter custom subject if needed
  if (!form.subject) {
    form.subject = template.subject
  }

  form.message = template.body + form.message
  showTemplates.value = false
  parsePlaceholdersForMessage()
  nextTick(() => editorRef.value.focus())
}

/**
 * Handle modal shown event
 * Each time the modal is shown we need to generate new draft id for the attachments
 */
function handleModalShown() {
  // If prevously there was to selected, use the same to as associations
  // e.q. open deal modal, close deal modal, open again, the form.to won't be associated
  if (form.to) {
    associateSelectedRecipients(form.to)
  }

  attachmentsDraftId.value = randomString()
}

/**
 * Handle modal shown hidden
 *
 * Reset the state, we need to reset the form and the
 * attachments because when the modal is hidden each time
 * new attachmentsDraftId is generated
 *
 * @return {Void}
 */
function modalHidden() {
  form.reset()

  // Add to again if there was TO recipients provided
  if (props.to) {
    form.to = props.to
  }

  attachments.value = []
  customAssociationsValue.value = {}
  emit('modal-hidden')
}

function send() {
  sendRequest(`/inbox/emails/${account.value.id}`).then(() =>
    Innoclapps.modal().hide('composeMessageModal')
  )
}

function prepareComponent() {
  store.dispatch('emailAccounts/fetch').then(accounts => {
    account.value = props.defaultAccount || accounts[0]
    componentReady.value = true
  })
}

prepareComponent()

defineExpose({
  subjectRef,
})
</script>
