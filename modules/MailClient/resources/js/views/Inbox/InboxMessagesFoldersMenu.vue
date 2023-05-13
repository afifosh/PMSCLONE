<template>
  <IVerticalNavigation>
    <InboxFoldersMenuItem
      v-for="folder in folders"
      :key="folder.id"
      :folder="folder"
    />
  </IVerticalNavigation>

  <!-- Creates a placeholders when not active folders are found -->
  <div v-if="!folders.length">
    <div
      :class="[
        'mb-2 rounded-md bg-neutral-200',
        placeholder === 2 || placeholder === 3 ? 'p-3' : 'p-4',
      ]"
      v-for="placeholder in [1, 2, 3, 4]"
      :key="'p-' + placeholder"
    />
  </div>
</template>
<script setup>
import InboxFoldersMenuItem from './InboxMessagesFoldersMenuItem.vue'

defineProps({
  folders: { type: Array, required: true },
})
</script>
