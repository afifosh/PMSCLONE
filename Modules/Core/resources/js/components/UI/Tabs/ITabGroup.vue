<template>
  <TabGroup :selected-index="modelValue" @change="handleChangeEvent">
    <slot></slot>
  </TabGroup>
</template>

<script setup>
import { TabGroup } from '@headlessui/vue'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({ modelValue: Number })

function handleChangeEvent(index) {
  emit('update:modelValue', index)
}
</script>
