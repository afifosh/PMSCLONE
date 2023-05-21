<template>
  <IModal
    :title="$t('calls::call.add')"
    size="md"
    v-model:visible="logCallModalIsVisible"
    :ok-title="$t('calls::call.add')"
    :ok-disabled="callBeingLogged"
    :cancel-title="$t('core::app.cancel')"
    @shown="logCallModalIsVisible = true"
    @hidden="logCallModalIsVisible = false"
    @ok="logCall"
  >
    <!-- re-render the fields as it's causing issue with the tinymce editor
                 on second time the editor has no proper height -->
    <div v-if="logCallModalIsVisible">
      <IOverlay :show="fields.isEmpty()">
        <FieldsGenerator
          :form-id="form.formId"
          view="create"
          :via-resource="resourceName"
          :via-resource-id="row.id"
          :fields="fields"
        />
      </IOverlay>
      <CreateFollowUpTask class="mt-2" :form="form" />
    </div>
  </IModal>
  <div class="inline-block">
    <IDropdown
      v-for="(phone, index) in row[column.attribute]"
      :key="index"
      no-caret
      :text="phone.number + (index != totalPhoneNumbers - 1 ? ', ' : '')"
    >
      <template #toggle="{ toggle }">
        <a
          class="link"
          @click.prevent="toggle"
          v-i-tooltip="$t('contacts::fields.phone.types.' + phone.type)"
          :href="'tel:' + phone.number"
          v-text="phone.number"
        />
      </template>
      <IDropdownItem
        v-i-tooltip="callDropdownTooltip"
        :disabled="!hasVoIPClient || !$gate.userCan('use voip')"
        @click="initiateNewCall(phone.number)"
        :text="$t('calls::call.make')"
      />
      <IButtonCopy
        :text="phone.number"
        :success-message="$t('contacts::fields.phone.copied')"
        tag="IDropdownItem"
      >
        {{ $t('core::app.copy') }}
      </IButtonCopy>
      <IDropdownItem
        :href="'tel:' + phone.number"
        :text="$t('core::app.open_in_app')"
      />
    </IDropdown>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue'
import propsDefinition from '~/Core/resources/js/components/Table/TableData/props'
import { useResourceFields } from '~/Core/resources/js/composables/useResourceFields'
import { useI18n } from 'vue-i18n'
import { useGate } from '~/Core/resources/js/composables/useGate'
import { useVoip } from '~/Core/resources/js/composables/useVoip'
import { useFieldsForm } from '~/Core/resources/js/components/Fields/useFieldsForm'
import CreateFollowUpTask from '~/Activities/resources/js/components/CreateFollowUpTask.vue'

const props = defineProps(propsDefinition)

const logCallModalIsVisible = ref(false)
const callBeingLogged = ref(false)

const { t } = useI18n()
const { gate } = useGate()
const { voip, hasVoIPClient } = useVoip()

const { fields, getCreateFields } = useResourceFields()

const { form } = useFieldsForm(fields, {
  task_date: null,
})

const callDropdownTooltip = computed(() => {
  if (!hasVoIPClient) {
    return t('core::app.integration_not_configured')
  } else if (gate.userCant('use voip')) {
    return t('calls::call.no_voip_permissions')
  }

  return ''
})

const totalPhoneNumbers = computed(
  () => props.row[props.column.attribute].length
)

async function initiateNewCall(phoneNumber) {
  form.set('task_date', null)

  let call = await voip.makeCall(phoneNumber)

  call.on('Disconnect', () => {
    logCallModalIsVisible.value = true
  })

  getCreateFields(Innoclapps.config('fields.groups.calls'), {
    viaResource: props.resourceName,
    viaResourceId: props.row.id,
  }).then(createFields => fields.value.set(createFields))
}

function logCall() {
  callBeingLogged.value = true

  form
    .set(props.resourceName, [props.row.id])
    .withQueryString({
      via_resource: props.resourceName,
      via_resource_id: props.row.id,
    })
    .hydrate()
    .post('/calls')
    .then(call => (logCallModalIsVisible.value = false))
    .finally(() => (callBeingLogged.value = false))
}
</script>
