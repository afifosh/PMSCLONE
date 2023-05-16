<template>
  <div :class="{ 'sync-stopped-by-system': isSyncStopped }">
    <ResourceTable
      resource-name="emails"
      ref="tableRef"
      :table-id="tableId"
      :row-class="rowClass"
      :action-request-params="actionRequestParams"
      :data-request-query-string="dataRequestQueryString"
    >
      <template #subject="{ row }">
        <MessageSubject
          :subject="row.subject"
          :message-id="row.id"
          :account-id="accountId"
        />
      </template>
      <template #to="{ row }">
        <MailRecipient
          v-for="(recipient, index) in row.to"
          :key="index"
          :recipient="recipient"
        />
        <span
          v-if="!row.to || row.to.length === 0"
          v-text="'(' + $t('mailclient::inbox.unknown_address') + ')'"
        />
      </template>
    </ResourceTable>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import ResourceTable from '~/Core/resources/js/components/Table'
import MessageSubject from './InboxMessageSubject.vue'
import MailRecipient from '../../Emails/MessageRecipient.vue'

defineProps({
  tableId: { required: true, type: String },
  dataRequestQueryString: { type: Object, required: true },
  actionRequestParams: { type: Object, required: true },
  accountId: { type: Number, required: true },
  isSyncStopped: Boolean,
})

const tableRef = ref(null)

function rowClass(row) {
  return !row.is_read ? 'unread' : 'read'
}

defineExpose({ tableRef })
</script>
<style>
.read td {
  font-weight: normal !important;
}
.unread td {
  font-weight: bold !important;
}
</style>
