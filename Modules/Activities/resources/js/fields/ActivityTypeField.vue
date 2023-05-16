<template>
  <FieldFormGroup :field="field" :field-id="fieldId" :form-id="formId">
    <IFormLabel
      :for="fieldId"
      class="mb-1 block sm:hidden"
      :label="$t('activities::activity.type.type')"
    />
    <IIconPicker
      v-model="value"
      :icons="typesForIconPicker"
      value-field="id"
      class="flex-nowrap overflow-auto sm:flex-wrap sm:overflow-visible"
      v-bind="field.attributes"
    />
  </FieldFormGroup>
</template>
<script setup>
import { ref, toRef, onMounted } from 'vue'
import FieldFormGroup from '~/Core/resources/js/components/Fields/FieldFormGroup.vue'
import propsDefinition from '~/Core/resources/js/components/Fields/props'
import { useField } from '~/Core/resources/js/components/Fields/useField'
import { useActivityTypes } from '../composables/useActivityTypes'

const props = defineProps(propsDefinition)

const { typesForIconPicker } = useActivityTypes()

const value = ref('')

function setInitialValue() {
  value.value = !(props.field.value === undefined || props.field.value === null)
    ? typeof props.field.value == 'number'
      ? props.field.value
      : props.field.value?.id
    : null
}

function handleChange(newValue) {
  let id = typeof newValue == 'number' ? newValue : newValue?.id

  value.value = id
  realInitialValue.value = id
}

const { field, fieldId, realInitialValue, initialize } = useField(
  value,
  toRef(props, 'field'),
  props.formId,
  toRef(props, 'isFloating'),
  {
    setInitialValue,
    handleChange,
  }
)

onMounted(initialize)
</script>
