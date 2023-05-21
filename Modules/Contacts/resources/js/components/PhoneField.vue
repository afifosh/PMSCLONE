<template>
  <FormFieldGroup
    :field="field"
    :label="label"
    :field-id="fieldId"
    :form-id="formId"
    class="multiple"
  >
    <IFormGroup
      v-for="(phone, index) in value"
      :key="index"
      class="rounded-md"
      v-show="!phone._delete"
    >
      <div class="flex">
        <div class="relative flex grow items-stretch focus-within:z-10">
          <div
            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
          >
            <Icon
              :icon="phone.type == 'mobile' ? 'DeviceMobile' : 'Phone'"
              class="h-5 w-5 text-neutral-500 dark:text-neutral-300"
            />
          </div>
          <IFormInput
            :rounded="false"
            class="rounded-l-md pl-10"
            @input="
              form.errors.clear(field.attribute + '.' + index + '.number'),
                searchDuplicateRecord(index, value[index].number)
            "
            :name="field.attribute + '.' + index + '.number'"
            v-model="value[index].number"
          />
        </div>
        <IDropdown adaptive-width>
          <template #toggle="{ toggle }">
            <IButton
              variant="white"
              class="relative -ml-px justify-between px-4 py-2 text-sm focus:z-10"
              :rounded="false"
              :size="false"
              @click="toggle"
            >
              {{
                value[index].type
                  ? field.types[value[index].type]
                  : $t('contacts::fields.phone.types.type')
              }}
              <Icon icon="ChevronDown" class="link h-4 w-4" />
            </IButton>
          </template>
          <IDropdownItem
            v-for="(label, id) in field.types"
            :key="id"
            @click="value[index].type = id"
            :text="label"
          />
        </IDropdown>
        <IButtonClose
          :rounded="false"
          @click="removePhone(index)"
          variant="white"
          class="relative -ml-px rounded-r-md"
        />
      </div>

      <IFormError
        v-text="form.getError(field.attribute + '.' + index + '.number')"
      />

      <IAlert
        v-if="duplicates[index]"
        dismissible
        @dismissed="duplicates[index] = null"
        class="mt-1"
      >
        <i18n-t
          scope="global"
          :keypath="field.checkDuplicatesWith.lang_keypath"
        >
          <template #display_name>
            <span class="font-medium">
              {{ duplicates[index].display_name }}
            </span>
          </template>
        </i18n-t>

        <div class="mt-4">
          <div class="-mx-2 -my-1.5 flex">
            <IButtonMinimal
              tag="a"
              :href="duplicates[index].path"
              target="_blank"
              variant="info"
              icon="ExternalLink"
              :text="$t('core::app.view_record')"
            />
          </div>
        </div>
      </IAlert>
    </IFormGroup>
    <div class="text-right">
      <a
        href="#"
        @click.prevent="newPhone"
        class="link mr-2 text-sm"
        v-t="'contacts::fields.phone.add'"
      />
    </div>
  </FormFieldGroup>
</template>
<script setup>
import { ref, toRef, computed, watch, onMounted } from 'vue'
import FormFieldGroup from '~/Core/resources/js/components/Fields/FieldFormGroup.vue'
import cloneDeep from 'lodash/cloneDeep'
import isEqual from 'lodash/isEqual'
import reject from 'lodash/reject'
import debounce from 'lodash/debounce'
import propsDefinition from '~/Core/resources/js/components/Fields/props'
import { useField } from '~/Core/resources/js/components/Fields/useField'
import { useForm } from '~/Core/resources/js/components/Fields/useFieldsForm'
const props = defineProps(propsDefinition)

const value = ref([])
const duplicates = ref({})
const form = useForm(props.formId)

const isDirty = computed(() => {
  if (!value.value) {
    return false
  }

  if (totalForDelete.value > 0) {
    return true
  }

  if (totalForInsert.value > 0) {
    return true
  }

  return !isEqual(value.value, realInitialValue.value)
})

/**
 * Get the predefined calling prefix
 */
const callingPrefix = computed(() => props.field.callingPrefix)

const totalForDelete = computed(
  () => value.value.filter(phone => phone._delete).length
)

const totalForInsert = computed(() => {
  return value.value.filter(
    phone =>
      !phone.id &&
      phone.number &&
      // Has only prefix value but the user did not added any number
      (!callingPrefix.value ||
        (callingPrefix.value && callingPrefix.value !== phone.number))
  ).length
})

const totalPhones = computed(() => value.value.length)

watch(totalPhones, newVal => {
  if (newVal === 0) {
    newPhone()
  }
})

function fill(form) {
  if (!callingPrefix.value) {
    form.fill(props.field.attribute, value.value)
    return
  }

  // Remove phones with only prefix
  form.fill(
    props.field.attribute,
    reject(cloneDeep(value.value), phone => {
      return (
        !phone._delete &&
        callingPrefix.value &&
        phone.number.trim() === callingPrefix.value.trim()
      )
    })
  )
}

function setInitialValue() {
  value.value = cloneDeep(props.field.value ? props.field.value : [])

  if (value.value.length === 0) {
    newPhone()
    realInitialValue.value = cloneDeep(value.value)
  }
}

function handleChange(val) {
  value.value = cloneDeep(val)

  if (totalPhones.value === 0) {
    newPhone()
  }

  realInitialValue.value = cloneDeep(value.value)
}

/**
 * Remove phone
 *
 * @param  {Number} index
 *
 * @return {Void}
 */
function removePhone(index) {
  duplicates.value[index] = null

  if (!value.value[index].id) {
    value.value.splice(index, 1)
  } else {
    value.value[index]._delete = true
  }

  form.errors.clear(props.field.attribute + '.' + index + '.number')

  if (
    totalPhones.value - totalForDelete.value === 0 ||
    (totalPhones.value - totalForDelete.value === 0 && totalForDelete.value > 0)
  ) {
    newPhone()
  }
}

/**
 * Add new phone
 *
 * @return {Void}
 */
function newPhone() {
  value.value.push({
    number: callingPrefix.value || '',
    type: props.field.type,
  })
}

/**
 * Search for duplicate record
 */
const searchDuplicateRecord = debounce((index, number) => {
  if (
    !checksForDuplicates.value ||
    props.view !== 'create' ||
    !number ||
    (callingPrefix.value && callingPrefix.value === number)
  ) {
    duplicates.value[index] = null
    return
  }

  makeDuplicateCheckRequest(number).then(
    duplicate => (duplicates.value[index] = duplicate)
  )
}, 700)

const {
  field,
  label,
  fieldId,
  realInitialValue,
  initialize,
  checksForDuplicates,
  makeDuplicateCheckRequest,
} = useField(
  value,
  toRef(props, 'field'),
  props.formId,
  toRef(props, 'isFloating'),
  {
    isDirty,
    handleChange,
    setInitialValue,
    fill,
  }
)

onMounted(initialize)
</script>
