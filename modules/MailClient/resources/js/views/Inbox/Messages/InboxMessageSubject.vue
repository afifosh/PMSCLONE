<template>
  <router-link
    class="text-neutral-900 hover:text-neutral-600 dark:text-neutral-100 dark:hover:text-neutral-300"
    :to="{
      name: 'inbox-message',
      params: {
        account_id: accountId,
        folder_id: folderId,
        id: messageId,
      },
    }"
  >
    {{ subject ? subject : '(' + $t('mailclient::inbox.no_subject') + ')' }}
  </router-link>
</template>
<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

defineProps(['subject', 'messageId', 'accountId'])

const route = useRoute()

const folderId = ref(route.params.folder_id)
</script>
