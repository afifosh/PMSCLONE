<template>
  <component
    :is="tag"
    class="block text-sm font-medium text-neutral-700 dark:text-neutral-100"
  >
    <span v-if="showRequiredMark" class="mr-1 text-sm text-danger-600">*</span>
    <slot>{{ label }}</slot>
  </component>
</template>
<script setup>
import { computed, useSlots } from 'vue'

const props = defineProps({
  tag: { default: 'label', type: [String, Object] },
  label: String,
  required: Boolean,
})

const slots = useSlots()

const showRequiredMark = computed(
  () => props.required && (props.label || slots.default())
)
</script>
