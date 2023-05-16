<template>
  <div class="mb-3">
    <slot name="label">
      <IFormLabel
        v-if="!optional && label"
        :for="labelFor"
        :label="label"
        :required="required"
        class="mb-1"
      />
      <div class="flex" v-else-if="label">
        <IFormLabel class="mb-1 grow" :for="labelFor" :label="label" />

        <span
          class="mb-px self-end text-xs text-neutral-600 dark:text-neutral-200"
          v-t="'core::app.optional'"
        />
      </div>
    </slot>
    <slot></slot>
    <IFormText v-if="description" class="mt-2" v-text="description" />
  </div>
</template>
<script setup>
defineProps({
  label: String,
  labelFor: String,
  description: String,
  optional: { default: false, type: Boolean },
  required: { default: false, type: Boolean },
})
</script>
