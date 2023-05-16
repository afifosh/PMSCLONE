<template>
  <FieldFormGroup
    :field="field"
    :label="label"
    :form-id="formId"
    :field-id="fieldId"
  >
    <div class="block">
      <div class="inline-block">
        <GuestsSelect
          v-model="value"
          ref="guestsSelectRef"
          :guests="
            view === 'update' || view === 'detail'
              ? field.activity.guests
              : undefined
          "
          :contacts="contacts"
        />
      </div>
    </div>
  </FieldFormGroup>
</template>
<script setup>
import { ref, toRef, computed, nextTick, onMounted } from 'vue'
import FieldFormGroup from '~/Core/resources/js/components/Fields/FieldFormGroup.vue'
import GuestsSelect from '../components/ActivityGuestsSelect.vue'
import propsDefinition from '~/Core/resources/js/components/Fields/props'
import { useField } from '~/Core/resources/js/components/Fields/useField'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'

const props = defineProps(propsDefinition)

const { record } = useRecordStore()

const guestsSelectRef = ref(null)
const value = ref('')

const contacts = computed(() => {
  if (!props.viaResource) {
    return []
  }

  return props.viaResource === 'contacts'
    ? [record.value]
    : record.value.contacts || []
})

function handleChange(val) {
  value.value = val
  realInitialValue.value = val

  // Checking it the ref set selected guest is visible as when
  // the form is resetting the field via the handleChange method the
  // field may be destroyed already if within v-if statement and will not exists
  nextTick(
    () => guestsSelectRef.value && guestsSelectRef.value.setSelectedGuests()
  )
}

function setInitialValue() {
  if (props.view === 'create' && props.viaResource === 'contacts') {
    // as hidden by default no need to set the initial value
    /*   value.value = {
          contacts: [record.value],
          users: [],
        }*/
  } else {
    value.value = !(
      props.field.value === undefined || props.field.value === null
    )
      ? props.field.value
      : {
          contacts: [],
          users: [],
        }
  }
}

const { field, label, fieldId, realInitialValue, initialize } = useField(
  value,
  toRef(props, 'field'),
  props.formId,
  toRef(props, 'isFloating'),
  {
    handleChange,
    setInitialValue,
  }
)

onMounted(initialize)
</script>
