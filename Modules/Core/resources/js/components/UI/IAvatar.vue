<template>
  <img :class="computedClasses" :src="src" />
</template>
<script setup>
import { computed } from 'vue'

const sizeMaps = {
  xs: 'h-5 w-5',
  sm: 'h-8 w-8',
  lg: 'h-10 w-10',
}

const props = defineProps({
  src: {
    type: String,
    required: true,
  },
  size: {
    default: 'sm',
    type: String,
    validator(value) {
      return ['xs', 'sm', 'lg'].includes(value)
    },
  },
})

const computedClasses = computed(() => [
  'inline-block rounded-full',
  sizeMaps[props.size],
])
</script>
