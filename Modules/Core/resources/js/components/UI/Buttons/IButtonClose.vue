<template>
  <IButton @click="$emit('click', $event)" :icon="icon" v-bind="$attrs" />
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
defineEmits(['click'])
defineProps({
  icon: { default: 'X', type: String },
})
</script>
