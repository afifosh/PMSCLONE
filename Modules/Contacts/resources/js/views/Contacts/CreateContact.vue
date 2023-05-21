<template>
  <CreateContactModal
    :title="modalTitle"
    :ok-title="
      hasSelectedExistingContact
        ? $t('core::app.associate')
        : $t('core::app.create')
    "
    :fields-visible="!hasSelectedExistingContact"
    :with-extended-submit-buttons="!hasSelectedExistingContact"
    :create-using="
      createFunc => (hasSelectedExistingContact ? associate() : createFunc())
    "
    @ready="handleReady"
  >
    <template #top="{ isReady }">
      <div
        v-if="viaResource"
        v-show="isReady"
        class="mb-4 rounded-md border border-success-400 px-4 py-3"
      >
        <FieldsGenerator
          :form-id="associateForm.formId"
          :fields="associateField"
          view="create"
        />
      </div>
    </template>
  </CreateContactModal>
</template>

<script setup>
import { computed } from 'vue'
import { useFieldsForm } from '~/Core/resources/js/components/Fields/useFieldsForm'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useResourceFields } from '~/Core/resources/js/composables/useResourceFields'
import { useI18n } from 'vue-i18n'
import { useApp } from '~/Core/resources/js/composables/useApp'

const emit = defineEmits(['associated'])

const props = defineProps({
  viaResource: String,
})

const { t } = useI18n()
const { setPageTitle } = useApp()

const { fields: associateField } = useResourceFields([
  {
    asyncUrl: '/contacts/search',
    attribute: 'id',
    component: 'SelectField',
    helpText: t('contacts::contact.associate_field_info'),
    helpTextDisplay: 'text',
    label: t('contacts::contact.contact'),
    labelKey: 'display_name',
    valueKey: 'id',
    lazyLoad: { url: '/contacts', params: { order: 'created_at|desc' } },
  },
])

const { form: associateForm } = useFieldsForm(associateField, {
  id: null,
})

const { record } = useRecordStore()

const hasSelectedExistingContact = computed(
  () => !!associateField.value.find('id').currentValue
)

const modalTitle = computed(() => {
  if (!props.viaResource) {
    return t('contacts::contact.create')
  }

  if (!hasSelectedExistingContact.value) {
    return t('contacts::contact.create_with', {
      name: record.value.display_name,
    })
  }

  return t('contacts::contact.associate_with', {
    name: record.value.display_name,
  })
})

function associate() {
  associateForm.hydrate()

  Innoclapps.request()
    .put(`associations/${props.viaResource}/${record.value.id}`, {
      contacts: [associateForm.id],
    })
    .then(({ data }) => {
      emit('associated', associateForm.id)

      Innoclapps.success(t('core::resource.associated'))
    })
}

function handleReady(fields) {
  setPageTitle(modalTitle)

  if (props.viaResource) {
    fields.value.updateValue(props.viaResource, [record.value])
  }
}
</script>
