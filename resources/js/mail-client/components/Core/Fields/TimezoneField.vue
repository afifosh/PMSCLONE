<template>
  <FieldFormGroup
    :field="field"
    :label="label"
    :field-id="fieldId"
    :form-id="formId"
  >
    <FormTimezone
      v-model="value"
      v-bind="field.attributes"
      :field-id="fieldId"
      :clearable="true"
      :name="field.attribute"
      :disabled="isReadonly"
    />
  </FieldFormGroup>
</template>
<script setup>
import { ref, toRef, onMounted } from 'vue'
import FieldFormGroup from './FieldFormGroup.vue'
import FormTimezone from '../FormTimezone.vue'
import propsDefinition from './props'
import { useField } from './useField'
import { useStore } from 'vuex'

const props = defineProps(propsDefinition)

const store = useStore()

const value = ref('')

const { field, label, fieldId, isReadonly, initialize } = useField(
  value,
  toRef(props, 'field'),
  props.formId,
  toRef(props, 'isFloating')
)

onMounted(initialize)
// Set the timezones from the field options in store, so the FormTimezone won't make request
// to actually retrieve the timezones from storage, e.q. useful also on web forms
store.commit('SET_TIMEZONES', props.field.timezones)
</script>
