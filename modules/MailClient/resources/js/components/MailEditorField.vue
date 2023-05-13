<template>
  <FieldFormGroup
    :field="field"
    :label="label"
    :field-id="fieldId"
    :form-id="formId"
  >
    <Editor :disabled="isReadonly" v-model="value" v-bind="field.attributes" />
  </FieldFormGroup>
</template>
<script setup>
import { ref, toRef, onMounted } from 'vue'
import FieldFormGroup from '~/Core/resources/js/components/Fields/FieldFormGroup.vue'
import propsDefinition from '~/Core/resources/js/components/Fields/props'
import { useField } from '~/Core/resources/js/components/Fields/useField'

const props = defineProps(propsDefinition)

const value = ref('')

const { field, fieldId, label, isReadonly, initialize } = useField(
  value,
  toRef(props, 'field'),
  props.formId,
  toRef(props, 'isFloating')
)

onMounted(initialize)
</script>
