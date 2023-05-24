<template>
  <span
    v-if="recipient.name"
    :class="[
      'text-neutral-700 dark:text-neutral-300',
      { 'border-b border-dashed border-neutral-400': recipient.address },
    ]"
    v-i-tooltip="recipient.address"
  >
    {{ recipient.name }}
  </span>
  <span
    v-else
    class="text-neutral-700 dark:text-neutral-300"
    v-text="recipient.address"
  />
</template>
<script setup>
defineProps({
  recipient: { type: Object, required: true },
})
</script>
