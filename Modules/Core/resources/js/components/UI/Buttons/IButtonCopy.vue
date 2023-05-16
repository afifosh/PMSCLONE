<template>
  <span v-show="isSupported">
    <component
      :is="tag"
      :icon="tag === 'IButtonIcon' ? $attrs.icon || 'Clipboard' : undefined"
      @click="performCopy"
    >
      <slot></slot>
    </component>
  </span>
</template>

<script setup>
import { toRef } from 'vue'
import { useClipboard } from '@vueuse/core'

const props = defineProps({
  text: String,
  tag: { type: [String, Object], default: 'IButtonIcon' },
  successMessage: {
    type: String,
    default: 'Text copied to clipboard.',
  },
})

const { copy, isSupported } = useClipboard({
  source: toRef(props, 'text'),
  legacy: true,
})

function performCopy() {
  copy()

  Innoclapps.info(props.successMessage)
}
</script>
