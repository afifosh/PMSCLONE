<template>
  <ITab
    :badge="record.unread_emails_for_user_count"
    badge-variant="info"
    :title="$t('mailclient::mail.emails')"
    icon="Mail"
  />
</template>
<script setup>
import { useRecordStore } from '../../composables/Core/useRecordStore'

const { record } = useRecordStore()
</script>
