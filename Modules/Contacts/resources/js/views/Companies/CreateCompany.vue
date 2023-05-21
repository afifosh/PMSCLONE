<template>
  <CreateCompanyModal
    :title="modalTitle"
    :ok-title="
      hasSelectedExistingCompany
        ? $t('core::app.associate')
        : $t('core::app.create')
    "
    :fields-visible="!hasSelectedExistingCompany"
    :with-extended-submit-buttons="!hasSelectedExistingCompany"
    :create-using="
      createFunc => (hasSelectedExistingCompany ? associate() : createFunc())
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
  </CreateCompanyModal>
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
    asyncUrl: '/companies/search',
    attribute: 'id',
    component: 'SelectField',
    helpText: t('contacts::company.associate_field_info'),
    helpTextDisplay: 'text',
    label: t('contacts::company.company'),
    labelKey: 'name',
    valueKey: 'id',
    lazyLoad: { url: '/companies', params: { order: 'created_at|desc' } },
  },
])

const { form: associateForm } = useFieldsForm(associateField, {
  id: null,
})

const { record } = useRecordStore()

const hasSelectedExistingCompany = computed(
  () => !!associateField.value.find('id').currentValue
)

const modalTitle = computed(() => {
  if (!props.viaResource) {
    return t('contacts::company.create')
  }

  if (!hasSelectedExistingCompany.value) {
    return t('contacts::company.create_with', {
      name: record.value.display_name,
    })
  }

  return t('contacts::company.associate_with', {
    name: record.value.display_name,
  })
})

function associate() {
  associateForm.hydrate()

  Innoclapps.request()
    .put(`associations/${props.viaResource}/${record.value.id}`, {
      companies: [associateForm.id],
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
