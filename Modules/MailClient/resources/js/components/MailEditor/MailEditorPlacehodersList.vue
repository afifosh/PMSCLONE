<template>
  <div>
    <FormInputSearch v-model="search" />

    <div class="mt-4 flex flex-wrap">
      <div
        v-for="placeholder in filteredPlaceholders"
        :key="groupName + placeholder.tag"
        class="mb-1 mr-1 flex items-center rounded border border-neutral-200 px-3 py-1 dark:border-neutral-700 dark:hover:border-neutral-600"
      >
        <a
          href="#"
          class="mr-1 text-sm text-neutral-600 hover:text-neutral-900 dark:text-neutral-200 dark:hover:text-neutral-400"
          @click.prevent="requestInsert(placeholder)"
          v-text="placeholder.description"
        />
        <IActionMessage
          v-show="
            justInsertedPlaceholder &&
            justInsertedPlaceholder.tag === placeholder.tag
          "
          message="Added!"
        />
      </div>
      <slot></slot>
    </div>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue'
const emit = defineEmits(['insert-requested'])
const props = defineProps(['placeholders', 'groupName'])
const search = ref(null)
const justInsertedPlaceholder = ref(null)

const filteredPlaceholders = computed(() => {
  if (!search.value) {
    return props.placeholders
  }

  return props.placeholders.filter(
    placeholder =>
      placeholder.description
        .toLowerCase()
        .includes(search.value.toLowerCase()) ||
      placeholder.tag.toLowerCase().includes(search.value.toLowerCase())
  )
})

function requestInsert(placeholder) {
  search.value = null
  justInsertedPlaceholder.value = placeholder
  emit('insert-requested', placeholder)
  setTimeout(() => (justInsertedPlaceholder.value = null), 3000)
}
</script>
