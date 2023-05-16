<template>
  <IDropdown no-caret :full="false" v-bind="$attrs">
    <template #toggle="{ toggle }">
      <IButtonIcon @click="toggle" :icon="icon" />
    </template>
    <div class="py-1">
      <slot></slot>
    </div>
  </IDropdown>
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: { default: 'vertical', type: String },
})

const icon = computed(() =>
  props.type === 'horizontal' ? 'DotsHorizontal' : 'DotsVertical'
)
</script>
