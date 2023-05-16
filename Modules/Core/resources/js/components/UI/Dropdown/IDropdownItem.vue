<template>
  <MenuItem v-slot="{ active: focused }" :disabled="disabled">
    <a
      :href="ahref"
      @click="handleClickEvent"
      :class="[
        'group block px-4 py-2 text-sm focus:outline-none',
        { 'group flex items-center': icon },
        { 'justify-between': icon && prependIcon },
        focused || active
          ? 'bg-primary-100 text-primary-700 hover:bg-primary-200 hover:text-primary-800'
          : 'text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-600 dark:hover:text-neutral-100',
        disabled ? 'pointer-events-none opacity-50' : null,
      ]"
    >
      <Icon
        v-if="icon && !prependIcon"
        :icon="icon"
        :class="[
          'mr-2 h-5 w-5 shrink-0',
          !(focused || active)
            ? 'text-neutral-500 group-hover:text-neutral-600 dark:text-neutral-300 dark:group-hover:text-neutral-100'
            : '',
        ]"
      />
      <slot>{{ text }}</slot>

      <Icon
        v-if="icon && prependIcon"
        :icon="icon"
        :class="[
          'ml-2 mt-px h-5 w-5 shrink-0',
          !(focused || active)
            ? 'text-neutral-500 group-hover:text-neutral-600 dark:text-neutral-300 dark:group-hover:text-neutral-100'
            : '',
        ]"
      />
    </a>
  </MenuItem>
</template>
<script setup>
import { inject, computed } from 'vue'
import { useRouter } from 'vue-router'
import { MenuItem } from '@headlessui/vue'

const emit = defineEmits(['click'])

const props = defineProps({
  active: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  icon: String,
  prependIcon: Boolean,
  href: String,
  text: String,
  to: [Object, String],
})

// IDropdown method
const hide = inject('hide')

const router = useRouter()

const ahref = computed(() => {
  if (props.href) {
    return props.href
  }

  if (props.to) {
    return router.resolve(props.to).href
  }

  return '#'
})

function handleClickEvent(e) {
  // Is it needed?
  if (props.disabled) {
    return
  }

  if ((!props.to && !props.href) || props.to) {
    e.preventDefault()
  }

  if (props.to) {
    router.push(props.to)
  }

  emit('click', e)

  if (!props.href) {
    hide()
  }
}
</script>
