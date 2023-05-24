<template>
  <!-- We will add custom option.parent_id in the label as
              if the folder name is duplicated the ICustomSelect addon won't work properly
              because ICustomSelect determines uniquness via the label.
              In thi case, we will provide custom function for getOptionLabel and will format
              the actual labels separtely via slots -->
  <IFormGroup :label="label" :required="required">
    <ICustomSelect
      :clearable="false"
      :options="folders"
      @update:modelValue="$emit('update:modelValue', $event)"
      :option-label-provider="
        option => '--' + option.parent_id + '--' + option.display_name
      "
      :reduce="folder => folder.id"
      :modelValue="modelValue"
    >
      <template #option="option">
        {{ option.display_name.replace('--' + option.parent_id + '--', '') }}
      </template>
      <template #selected-option="option">
        {{ option.display_name.replace('--' + option.parent_id + '--', '') }}
      </template>
    </ICustomSelect>
    <IFormError v-text="form.getError(field)" />
  </IFormGroup>
</template>
<script setup>
defineEmits(['update:modelValue'])
defineProps(['modelValue', 'form', 'field', 'folders', 'label', 'required'])
</script>
