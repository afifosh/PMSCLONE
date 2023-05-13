<template>
  <div
    :class="{
      'sticky top-0 bg-opacity-75 p-2 backdrop-blur-lg backdrop-filter dark:bg-neutral-500/80':
        !messageInfoIsFullyVisible,
    }"
  />
  <div class="sticky top-2 z-auto">
    <div
      :class="[
        !messageInfoIsFullyVisible
          ? 'border-t border-neutral-200 bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-900'
          : 'rounded-t-lg bg-white dark:bg-neutral-900',
        'overflow-hidden shadow',
      ]"
    >
      <div
        :class="{ blur: isLoading }"
        class="flex flex-col items-center border-b border-neutral-200 px-4 py-5 dark:border-neutral-800 sm:flex-row sm:p-5"
      >
        <div class="mr-3 grow">
          <h5
            class="text-lg/6 font-semibold text-neutral-800 dark:text-white"
            v-text="subject"
          />
          <AssociationsPopover
            :disabled="syncingAssociations"
            @change="syncAssociations"
            class="inline-flex"
            placement="bottom-start"
            :associated="message.associations"
          />
        </div>

        <div class="shrink-0">
          <div
            v-if="componentReady"
            class="flex flex-col items-center space-y-2 sm:flex-row sm:flex-wrap sm:space-x-2 sm:space-y-0"
          >
            <div v-show="!account.is_sync_stopped">
              <Actions
                type="dropdown"
                :ids="message.id"
                :actions="message.actions"
                :action-request-params="actionRequestParams"
                resource-name="emails"
                @run="actionExecuted"
              />
            </div>

            <div class="flex items-center space-x-3">
              <IButton
                v-if="canReply"
                variant="white"
                size="sm"
                :disabled="account.is_sync_stopped"
                @click="reply(true)"
                icon="Reply"
                :text="$t('mailclient::inbox.reply')"
              />
              <!-- TODO, find reply-all icon -->
              <IButton
                v-if="canReply && hasMoreReplyTo"
                variant="white"
                size="sm"
                :disabled="account.is_sync_stopped"
                @click="replyAll()"
                icon="Reply"
                :text="$t('mailclient::inbox.reply_all')"
              />
              <IButton
                v-if="canReply"
                variant="white"
                size="sm"
                icon="Share"
                :disabled="account.is_sync_stopped"
                @click="forward(true)"
                :text="$t('mailclient::inbox.forward')"
              />
              <IMinimalDropdown type="horizontal">
                <IDropdownItem
                  @click="showCreateContactForm"
                  icon="Users"
                  :text="$t('contacts::contact.convert')"
                />
                <IDropdownItem
                  @click="showCreateDealForm"
                  icon="Banknotes"
                  :text="$t('deals::deal.create')"
                />
                <IDropdownItem
                  @click="prepareActivityCreate"
                  icon="Calendar"
                  :text="$t('activities::activity.create')"
                />
              </IMinimalDropdown>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <ICard
    id="messageInfo"
    class="mb-3 rounded-b-md"
    :rounded="false"
    :overlay="isLoading"
  >
    <div class="flex" v-if="componentReady">
      <div class="mr-2">
        <IAvatar v-once :src="message.avatar_url" />
      </div>
      <div class="grow">
        <MessageRecipients
          v-once
          :label="$t('mailclient::inbox.from')"
          :recipients="message.from"
        />
        <MessageRecipients
          v-once
          :label="$t('mailclient::inbox.reply_to')"
          :recipients="message.reply_to"
          :show-when-empty="false"
        />
        <MessageRecipients
          :label="$t('mailclient::inbox.to')"
          :recipients="message.to"
        />
        <MessageRecipients
          v-once
          :label="$t('mailclient::inbox.cc')"
          :recipients="message.cc"
          :show-when-empty="false"
        />
        <MessageRecipients
          v-once
          :label="$t('mailclient::inbox.bcc')"
          :recipients="message.bcc"
          :show-when-empty="false"
        />
        <p class="mt-2 text-sm text-neutral-800 dark:text-neutral-100">
          <span class="mr-1 font-semibold">
            {{ $t('mailclient::inbox.date') }}:
          </span>
          <span
            class="text-neutral-700 dark:text-neutral-300"
            v-once
            v-text="localizedDateTime(message.date)"
          />
        </p>
      </div>
      <IBadge
        v-if="message.opened_at"
        v-once
        variant="success"
        wrapper-class="flex self-start"
      >
        <Icon icon="Eye" class="mr-1 h-5 w-5" />
        <span class="mr-1"> ({{ message.opens }}) - </span>
        {{ localizedDateTime(message.opened_at) }}
      </IBadge>
    </div>
  </ICard>

  <ICard class="mb-3" :overlay="isLoading">
    <MessagePreview
      v-if="!isLoading"
      :visible-text="message.visible_text"
      :hidden-text="message.hidden_text"
    />
  </ICard>

  <div v-if="hasAttachments" class="mb-3 mt-5">
    <dd
      class="mb-2 text-sm font-medium leading-6 text-neutral-900 dark:text-neutral-100"
      v-t="'mailclient.mail.attachments'"
    />
    <MessageAttachments :attachments="message.media" />
  </div>

  <MessageReply
    v-if="canReply"
    :message="message"
    :visible="isReplying || isForwarding"
    :to-all="replyToAll"
    :forward="isForwarding"
    @modal-hidden="replyModalHidden"
  />

  <CreateDealModal
    @created="getMessage(), (dealIsBeingCreated = false)"
    v-model:visible="dealIsBeingCreated"
    :associations="{ emails: [message.id] }"
    :contacts="relatedContacts"
    :name="message.subject"
  />

  <CreateContactModal
    @modal-hidden="createContactBindings = {}"
    @created="getMessage(), (contactIsBeingCreated = false)"
    v-model:visible="contactIsBeingCreated"
    :associations="{ emails: [message.id] }"
    v-bind="createContactBindings"
  />

  <CreateActivityModal
    v-if="activityIsBeingCreated"
    @created="getMessage(), (activityIsBeingCreated = false)"
    @modal-hidden="activityIsBeingCreated = false"
    :contacts="activityCreateContacts"
    :title="
      $t('activities::activity.title_via_create_message', {
        subject: message.subject,
      })
    "
    :message="message"
  />
</template>
<script setup>
import { ref, shallowRef, computed, onMounted, onBeforeUnmount } from 'vue'
import MessageRecipients from '../../Emails/MessageRecipients.vue'
import MessageReply from '../../Emails/MessageReply.vue'
import MessageAttachments from '../../Emails/MessageAttachments.vue'
import MessagePreview from '../../Emails/MessagePreview.vue'
import Actions from '~/Core/resources/js/components/Actions/Actions.vue'
import AssociationsPopover from '~/Core/resources/js/components/AssociationsPopover.vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { useApp } from '~/Core/resources/js/composables/useApp'
import { useI18n } from 'vue-i18n'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useLoader } from '~/Core/resources/js/composables/useLoader'

const props = defineProps({
  account: { required: true, type: Object },
})

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const store = useStore()

const { setPageTitle } = useApp()
const { localizedDateTime } = useDates()
const { setLoading, isLoading } = useLoader()

const message = ref({})
const dealIsBeingCreated = ref(false)
const contactIsBeingCreated = ref(false)
const activityIsBeingCreated = ref(false)
const isReplying = ref(false)
const isForwarding = ref(false)
const replyToAll = ref(false)
const syncingAssociations = ref(false)
const messageInfoIsFullyVisible = ref(false)
const relatedContacts = ref([])
const activityCreateContacts = shallowRef([])
const createContactBindings = ref({})

let scrollObserver = null

/**
 * Checks whether the component is ready based if the message
 * data has keys, if don't means that it's not yet fetched
 */
const componentReady = computed(() => Object.keys(message.value).length > 0)

const hasMoreReplyTo = computed(
  () => message.value.cc && message.value.cc.length > 0
)

/**
 * Provides the actions request query string
 */
const actionRequestParams = computed(() => ({
  folder_id: route.params.folder_id,
  account_id: route.params.account_id,
}))

/**
 * Check whether a reply can be performed to this message
 */
const canReply = computed(() => componentReady.value && !message.value.is_draft)

/**
 * Get the message subject
 */
const subject = computed(() => {
  if (!message.value.subject) {
    return t('mailclient::inbox.no_subject')
  }

  return message.value.subject
})

/**
 * Check whether the message has attachments
 */
const hasAttachments = computed(
  () => componentReady.value && message.value.media.length > 0
)

/**
 * Get the message route
 */
const messageRoute = computed(
  () => `/inbox/emails/folders/${route.params.folder_id}/${route.params.id}`
)

/**
 * Get the total unread messages for all accounts
 */
const totalUnreadMessages = computed(
  () => store.getters.getMenuItem('inbox').badge
)

/**
 * Prepare data for activity create
 */
async function prepareActivityCreate() {
  let { data: contacts } = await Innoclapps.request().get('contacts/search', {
    params: {
      q: message.value.from.address,
      search_fields: 'email:=',
    },
  })

  activityCreateContacts.value = contacts
  activityIsBeingCreated.value = true
}
/**
 * Initiate show create deal form
 */
async function showCreateDealForm() {
  relatedContacts.value = await searchRelatedContacts()
  dealIsBeingCreated.value = true
}

/**
 * Initiate show create contact form
 */
function showCreateContactForm() {
  contactIsBeingCreated.value = true
  createContactBindings.value['email'] = message.value.from.address
  if (Boolean(message.value.from.name)) {
    createContactBindings.value['first-name'] =
      message.value.from.name.split(' ')[0]
    createContactBindings.value['last-name'] =
      message.value.from.name.split(' ')[1] || null
  }
}

/**
 * Search the related message contacts
 */
async function searchRelatedContacts() {
  let { data: contacts } = await Innoclapps.request().get('contacts/search', {
    params: {
      q: message.value.from.address,
      search_fields: 'email:=',
    },
  })

  return contacts
}

/**
 * Handle the reply modal hidden event
 *
 * @return {Void}
 */
function replyModalHidden() {
  reply(false)
  forward(false)
}

/**
 * Sync the email associations
 *
 * @param  {Object} data
 *
 * @return {Void}
 */
function syncAssociations(data) {
  syncingAssociations.value = true

  Innoclapps.request()
    .post(`associations/emails/${message.value.id}`, data)
    .finally(() => (syncingAssociations.value = false))
}

/**
 * Handle action executed event
 *
 * @param  {Object} action
 *
 * @return {Void}
 */
function actionExecuted(action) {
  // After a move action is executed we need to change the route
  // to the actual new folder, to prevent showing error message e.q. MessageNotFound
  // when executing move action again as the old folder will be passed to the params request
  if (action.uriKey === 'email-account-message-move') {
    replaceMessageRouteFolder(action.response.moved_to_folder_id)
  } else if (action.uriKey === 'email-account-message-delete') {
    // Message parmanently deleted, navigate to inbox
    if (route.params.folder_id === props.account.trash_folder.id) {
      router.replace({
        name: 'inbox',
      })
    } else {
      replaceMessageRouteFolder(props.account.trash_folder.id)
    }
  }
}

/**
 * Replace the current route folder id
 *
 * @param  {Number} folderId
 *
 * @return {Void}
 */
function replaceMessageRouteFolder(folderId) {
  router.replace({
    name: 'inbox-message',
    params: {
      account_id: props.account.id,
      folder_id: folderId,
      id: message.value.id,
    },
  })
}

/**
 * Change reply data state
 *
 * @param  {Boolean} state
 *
 * @return {Void}
 */
function reply(state = true) {
  isReplying.value = state
  replyToAll.value = false
}

/**
 * Change forward data state
 *
 * @param  {Boolean} state
 *
 * @return {Void}
 */
function forward(state = true) {
  isForwarding.value = state
}

/**
 * Initialize reply all to mesage
 *
 * @return {Void}
 */
function replyAll() {
  isReplying.value = true
  replyToAll.value = true
}

/**
 * Get the message from storage
 *
 * @return {Void}
 */
async function getMessage() {
  setLoading(true)

  let { data } = await Innoclapps.request().get(messageRoute.value, {
    // Pass params for the actions
    params: {
      account_id: props.account.id,
      folder_id: route.params.folder_id,
    },
  })

  message.value = data

  // Update the active folder so unread/read keys
  // can be updated too for the folders menu
  store.commit('emailAccounts/UPDATE', {
    id: props.account.id,
    item: {
      ...props.account,
      active_folders_tree: data.account_active_folders_tree,
    },
  })

  if (data.was_unread === true) {
    store.dispatch(
      'emailAccounts/updateUnreadCountUI',
      totalUnreadMessages.value === 0 ? 0 : totalUnreadMessages.value - 1
    )
  }

  setPageTitle(subject.value)
  setLoading(false)
}

getMessage()

onMounted(() => {
  scrollObserver = new IntersectionObserver(
    entries => {
      messageInfoIsFullyVisible.value = entries[0].isIntersecting
    },
    {
      root: document.getElementById('main'),
      threshold: 1,
    }
  )
  scrollObserver.observe(document.getElementById('messageInfo'))
})

onBeforeUnmount(() => {
  if (scrollObserver) {
    scrollObserver.unobserve(document.getElementById('messageInfo'))
    scrollObserver = null
  }
})
</script>
