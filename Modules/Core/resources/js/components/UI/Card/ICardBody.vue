<template>
  <div :class="condensed ? 'px-6 py-3' : 'px-4 py-5 sm:px-6'">
    <slot></slot>
  </div>
</template>
<script setup>
const props = defineProps({ condensed: { type: Boolean, default: false } })
</script>
