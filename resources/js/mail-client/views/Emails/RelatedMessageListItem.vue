<template>
  <ICard
    :class="'email-' + email.id"
    condensed
    v-observe-visibility="{
      callback: handleVisibilityChanged,
      once: true,
      throttle: 300,
      intersection: {
        threshold: 0.5,
      },
    }"
  >
    <template #header>
      <div class="flex space-x-1">
        <div class="inline-flex grow flex-col">
          <h3
            :class="[!email.is_read ? 'font-bold' : 'font-semibold']"
            class="truncate whitespace-normal text-base/6 font-medium text-neutral-700 dark:text-white md:text-lg"
          >
            <span
              v-once
              v-text="
                email.subject
                  ? email.subject
                  : '(' + $t('mailclient::inbox.no_subject') + ')'
              "
            />
          </h3>
          <span class="text-sm text-neutral-500 dark:text-neutral-300" v-once>
            {{ localizedDateTime(email.date) }}
          </span>
          <div class="flex">
            <AssociationsPopover
              :resource-name="viaResource"
              :associateable="record"
              :disabled="syncingAssociations"
              @change="syncAssociations"
              :associated="email.associations"
            />
          </div>
        </div>
        <div class="ml-3 flex shrink-0 items-center self-start">
          <IBadge
            v-if="email.opened_at"
            v-once
            variant="success"
            wrapper-class="mr-1 flex"
          >
            <Icon icon="Eye" class="mr-1 h-5 w-5" />
            <span class="mr-1"> ({{ email.opens }}) - </span>
            {{ localizedDateTime(email.opened_at) }}
          </IBadge>
          <IMinimalDropdown>
            <IDropdownItem
              @click="
                $router.push({
                  name: 'inbox-message',
                  params: {
                    id: email.id,
                    account_id: email.email_account_id,
                    folder_id: email.folders[0].id,
                  },
                })
              "
              :text="$t('mailclient::mail.view')"
            />

            <IDropdownItem @click="destroy" :text="$t('core::app.delete')" />
          </IMinimalDropdown>
        </div>
      </div>
    </template>
    <MessageRecipients
      v-once
      :label="$t('mailclient::inbox.from')"
      :recipients="email.from"
    />
    <div class="flex">
      <div>
        <MessageRecipients
          v-once
          :label="$t('mailclient::inbox.to')"
          :recipients="email.to"
        />
      </div>
      <div class="-mt-0.5 ml-3">
        <IPopover placement="top" class="w-72">
          <button
            type="button"
            class="link text-sm focus:outline-none"
            v-t="'core::app.details'"
          />
          <template #popper>
            <div class="flex flex-col px-4 py-3">
              <MessageRecipients
                v-once
                :label="$t('mailclient::inbox.from')"
                :recipients="email.from"
              />
              <MessageRecipients
                v-once
                :label="$t('mailclient::inbox.to')"
                :recipients="email.to"
              />
              <MessageRecipients
                v-once
                :label="$t('mailclient::inbox.reply_to')"
                :recipients="email.reply_to"
                :show-when-empty="false"
              />
              <MessageRecipients
                v-once
                :label="$t('mailclient::inbox.cc')"
                :recipients="email.cc"
                :show-when-empty="false"
              />
              <MessageRecipients
                v-once
                :label="$t('mailclient::inbox.bcc')"
                :recipients="email.bcc"
                :show-when-empty="false"
              />
            </div>
          </template>
        </IPopover>
      </div>
    </div>

    <div class="mail-text all-revert" v-once>
      <div class="font-sans text-sm leading-[initial] dark:text-white">
        <TextCollapse
          v-if="email.visible_text"
          :text="email.visible_text"
          lightbox
          :length="250"
          class="mt-3"
        />

        <div class="clear-both"></div>

        <HiddenText :text="email.hidden_text" />
      </div>
    </div>

    <div
      v-if="email.media.length > 0"
      v-once
      class="-mx-6 mb-3 mt-4 border-t border-neutral-200 px-6 pt-4 dark:border-neutral-700"
    >
      <dd
        class="mb-2 text-sm font-medium leading-6 text-neutral-900 dark:text-neutral-100"
        v-t="'mailclient.mail.attachments'"
      />

      <MessageAttachments :attachments="email.media" />
    </div>
    <template #footer>
      <div class="clear-both"></div>
      <div class="flex divide-x divide-neutral-200 dark:divide-neutral-700">
        <a
          href="#"
          class="link flex items-center text-sm"
          @click.prevent="reply(true)"
        >
          <Icon icon="Reply" class="mr-1.5 h-4 w-4" />
          {{ $t('mailclient::inbox.reply') }}
        </a>
        <a
          v-if="hasMoreReplyTo"
          href="#"
          class="link ml-2 flex items-center pl-2 text-sm"
          @click.prevent="replyAll"
        >
          <Icon icon="Reply" class="mr-1.5 h-4 w-4" />
          {{ $t('mailclient::inbox.reply_all') }}
        </a>
        <a
          href="#"
          class="link ml-2 flex items-center pl-2 text-sm"
          @click.prevent="forward(true)"
        >
          <Icon icon="Share" class="mr-1.5 h-4 w-4" />
          {{ $t('mailclient::inbox.forward') }}
        </a>
      </div>
    </template>

    <MessageReply
      :message="email"
      :visible="isReplying || isForwarding"
      :forward="isForwarding"
      :resource-name="viaResource"
      :resource-record="record"
      :to-all="replyToAll"
      @modal-hidden="handleReplyModalHidden"
    />
  </ICard>
</template>
<script setup>
import { ref, computed } from 'vue'
import MessageAttachments from './MessageAttachments.vue'
import MessageRecipients from './MessageRecipients.vue'
import MessageReply from './MessageReply.vue'
import HiddenText from './MessageHiddenText.vue'
import AssociationsPopover from '../../components/Core/AssociationsPopover.vue'
import { ObserveVisibility } from 'vue-observe-visibility'
import { useStore } from 'vuex'
import { useRecordStore } from '../../composables/Core/useRecordStore'
import { useDates } from '../../composables/useDates'

const vObserveVisibility = ObserveVisibility

const props = defineProps({
  viaResource: { type: String, required: true },
  email: { required: true, type: Object },
})

const store = useStore()

const { localizedDateTime } = useDates()

const {
  record,
  updateResourceRecordHasManyRelationship,
  decrementResourceRecordCount,
  removeResourceRecordHasManyRelationship,
} = useRecordStore()

const isReplying = ref(false)
const isForwarding = ref(false)
const replyToAll = ref(false)
const syncingAssociations = ref(false)

const hasMoreReplyTo = computed(
  () => props.email.cc && props.email.cc.length > 0
)

function handleReplyModalHidden() {
  // Allow timeout because the hidden blur sometimes is not removed
  setTimeout(() => {
    reply(false)
    forward(false)
  }, 300)
}

function handleVisibilityChanged(isVisible, entry) {
  if (isVisible && !props.email.is_read) {
    Innoclapps.request()
      .post(`/emails/${props.email.id}/read`)
      .then(({ data }) => {
        updateResourceRecordHasManyRelationship(data, 'emails')
        decrementResourceRecordCount('unread_emails_for_user_count')
        store.dispatch('emailAccounts/decrementUnreadCountUI')
      })
  }
}

async function destroy() {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(`/emails/${props.email.id}`)

  if (!props.email.is_read) {
    decrementResourceRecordCount('unread_emails_for_user_count')
    store.dispatch('emailAccounts/decrementUnreadCountUI')
  }

  removeResourceRecordHasManyRelationship(props.email.id, 'emails')
}

function syncAssociations(data) {
  syncingAssociations.value = true
  Innoclapps.request()
    .post(`associations/emails/${props.email.id}`, data)
    .then(({ data }) => updateResourceRecordHasManyRelationship(data, 'emails'))
    .finally(() => (syncingAssociations.value = false))
}

function reply(state = true) {
  isReplying.value = state
  replyToAll.value = false
}

function forward(state = true) {
  isForwarding.value = state
}

function replyAll() {
  isReplying.value = true
  replyToAll.value = true
}
</script>
