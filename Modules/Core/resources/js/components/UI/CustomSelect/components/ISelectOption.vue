<template>
  <li
    role="option"
    :id="`cs${uid}__option-${index}`"
    :aria-selected="isHighlighted ? true : null"
  >
    <a
      href="#"
      @click.prevent="$emit('selected')"
      @mouseover.self.passive="
        isSelectable ? $emit('typeAheadPointer', index) : null
      "
      :class="[
        'group block px-4 py-2 text-sm focus:outline-none',
        computedClasses,
      ]"
    >
      <component
        :is="swatchColor ? TextBackground : 'span'"
        :color="swatchColor || undefined"
        :class="
          swatchColor
            ? 'inline-flex items-center justify-center rounded-full px-2.5 font-normal leading-5 dark:!text-white'
            : null
        "
      >
        <slot :label="label">
          {{ label }}
        </slot>
      </component>
    </a>
  </li>
</template>
<script setup>
import { computed } from 'vue'
import TextBackground from '~/Core/resources/js/components/TextBackground.vue'

const emit = defineEmits(['typeAheadPointer', 'selected'])

const props = defineProps([
  'label',
  'uid',
  'index',
  'active',
  'isSelected',
  'isSelectable',
  'swatchColor',
])

const isHighlighted = computed(() => props.isSelected || props.active)

const computedClasses = computed(() => ({
  'bg-primary-100 text-primary-700 hover:bg-primary-200 hover:text-primary-800':
    isHighlighted.value,
  'text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-600 dark:hover:text-neutral-100':
    !isHighlighted.value,
  'pointer-events-none opacity-50': !props.isSelectable,
}))
</script>
