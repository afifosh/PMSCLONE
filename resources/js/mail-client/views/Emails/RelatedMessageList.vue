<template>
  <div class="space-y-3">
    <div v-for="email in emails" :key="email.id">
      <Email :email="email" :via-resource="viaResource" />
    </div>
  </div>
</template>
<script setup>
import Email from './RelatedMessageListItem.vue'

defineProps({
  emails: Array,
  viaResource: { required: true, type: String },
})
</script>
