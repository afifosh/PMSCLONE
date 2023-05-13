<template>
  <component
    :is="tableId === 'messages-incoming' ? MessagesIncoming : MessagesOutgoing"
    ref="tableComponentRef"
    :table-id="tableId"
    :account-id="account.id"
    :is-sync-stopped="account.is_sync_stopped"
    :data-request-query-string="tableDataRequestQueryString"
    :action-request-params="actionRequestParams"
  />
</template>
<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import MessagesOutgoing from './InboxMessagesTableOutgoing.vue'
import MessagesIncoming from './InboxMessagesTableIncoming.vue'
import find from 'lodash/find'
import { useRoute } from 'vue-router'
import { useApp } from '~/Core/resources/js/composables/useApp'
import { useTable } from '~/Core/resources/js/components/Table/useTable'
import { useGlobalEventListener } from '~/Core/resources/js/composables/useGlobalEventListener'

const props = defineProps({
  account: { type: Object, required: true },
})

const route = useRoute()

const { setPageTitle } = useApp()
const { reloadTable } = useTable()

const folderId = ref(null)
const accountId = ref(null)
const folder = ref({})
const tableComponentRef = ref(null)

const actionRequestParams = computed(() => ({
  folder_id: folderId.value,
  account_id: accountId.value,
}))

const tableDataRequestQueryString = computed(() => ({
  account_id: accountId.value,
  folder_id: folderId.value,
  folder_type: folderType.value,
}))

const folderType = computed(() => {
  if (isOutgoingFolderType.value) {
    return 'outgoing'
  } else if (isIncomingFolderType.value) {
    return 'incoming'
  }

  return Innoclapps.config('mail.folders.other')
})

const tableId = computed(() => {
  return folderType.value === 'incoming' ||
    folderType.value === Innoclapps.config('mail.folders.other')
    ? 'messages-incoming'
    : 'messages-outgoing'
})

/**
 * Checks whether the current folder of type outgoing
 * The computed also checks whether this folder is child in outgoing folder
 */
const isOutgoingFolderType = computed(() => {
  let currentFolderIsOutgoing =
    Innoclapps.config('mail.folders.outgoing').indexOf(folder.value.type) > -1

  if (currentFolderIsOutgoing) {
    return true
  }

  // Look more deeply to see if this is a child of the sent folder
  return isFolderChildIn('outgoing')
})

/**
 * Checks whether the current folder of type incoming
 * The computed also checks whether this folder is child in incoming folder
 */
const isIncomingFolderType = computed(() => {
  let currentFolderIsIncoming =
    Innoclapps.config('mail.folders.incoming').indexOf(folder.value.type) > -1

  if (currentFolderIsIncoming) {
    return true
  }

  // Look more deeply to see if this is a child of the sent folder
  return isFolderChildIn('incoming')
})

/**
 * Check hierarchically whether the current folder
 * is a deep child of the the sent folder
 *
 * @param  {String}  The key name, to use for the check incoming or outgoing
 * @param  {Object|null}  hierarchicalFolder
 *
 * @return {Boolean}
 */
function isFolderChildIn(key, hierarchicalFolder) {
  let folderBeingChecked = hierarchicalFolder || folder.value

  if (!folderBeingChecked.parent_id) {
    return false
  }

  let parent = find(props.account.folders, [
    'id',
    Number(folderBeingChecked.parent_id),
  ])

  if (Innoclapps.config(`mail.folders.${key}`).indexOf(parent.type) > -1) {
    return true
  } else if (parent.parent_id) {
    return isFolderChildIn(key, parent)
  }

  return false
}

function reload() {
  reloadTable(tableId.value)
}

/**
 * When the user is viewing directly e.q. the sent folder
 * after the message is sent, we need to reload the folder
 *
 * @return {Void}
 */
function reloadOutgoingFolderTable() {
  if (isOutgoingFolderType.value) {
    reload()
  }
}

watch(
  () => route.params,
  (newVal, oldVal) => {
    const samePageNavigation =
      newVal.account_id && newVal.folder_id && !newVal.id // we will check if there is a message id, if yes, then it's another page
    accountId.value = newVal.account_id
    folderId.value = newVal.folder_id

    if (!oldVal || (oldVal && oldVal.folder_id != newVal.folder_id)) {
      folder.value = find(
        props.account.folders,
        folder => Number(folder.id) === Number(newVal.folder_id)
      )
    }

    if (samePageNavigation) {
      nextTick(() =>
        setPageTitle(`${folder.value.display_name} - ${props.account.email}`)
      )
    }

    // We need to refetch the table settings
    // when an account has been changed because of the MOVE TO
    // action is using the request params to compose the field options
    if (
      samePageNavigation &&
      oldVal &&
      Number(newVal.account_id) !== Number(oldVal.account_id)
    ) {
      nextTick(() => tableComponentRef.value.tableRef.refetchActions())
    }
  },
  { immediate: true }
)

useGlobalEventListener('user-synchronized-email-account', reload)
useGlobalEventListener('email-accounts-sync-finished', reload)
useGlobalEventListener('email-sent', reloadOutgoingFolderTable)
</script>
<style>
.sync-stopped-by-system table .form-check {
  pointer-events: none;
  opacity: 0.5;
}
</style>
