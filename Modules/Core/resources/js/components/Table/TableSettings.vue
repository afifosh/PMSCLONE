<template>
  <div class="mb-2 md:mb-0 md:w-auto">
    <IButton
      v-if="config.customizeable && withCustomizeButton"
      variant="white"
      icon="Cog"
      @click="() => $refs.settingsRef.showModal()"
    />
  </div>

  <!-- Settings Modal -->
  <CustomizeTableSettings
    ref="settingsRef"
    :config="config"
    :table-id="tableId"
    :url-path="urlPath"
  />
</template>
<script setup>
import CustomizeTableSettings from './TableSettingsCustomization.vue'

defineProps({
  config: { required: true, type: Object },
  tableId: { required: true, type: String },
  urlPath: { required: true, type: String },
  withCustomizeButton: { type: Boolean, default: false },
  resourceName: { type: String, required: true },
})
</script>
