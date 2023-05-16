<template>
  <ISlideover
    @hidden="handleModalHiddenEvent"
    @shown="handleModalShownEvent"
    :visible="visible"
    @update:visible="$emit('update:visible', $event)"
    :title="title || $t('activities::activity.create')"
    :ok-title="$t('core::app.create')"
    :ok-disabled="form.busy"
    :cancel-title="$t('core::app.cancel')"
    :initial-focus="modalCloseElement"
    id="createActivityModal"
    static-backdrop
    form
    @submit="create"
  >
    <FieldsPlaceholder v-if="fields.isEmpty()" />

    <FieldsGenerator
      focus-first
      :form-id="form.formId"
      view="create"
      :is-floating="true"
      :fields="fields"
    >
      <template #after-deals-field>
        <span class="-mt-2 block text-right">
          <a
            href="#"
            @click.prevent="dealBeingCreated = true"
            class="link text-sm"
          >
            + {{ $t('deals::deal.create') }}
          </a>
        </span>
      </template>

      <template #after-companies-field>
        <span class="-mt-2 block text-right">
          <a
            href="#"
            @click.prevent="companyBeingCreated = true"
            class="link text-sm"
          >
            + {{ $t('contacts::company.create') }}
          </a>
        </span>
      </template>

      <template #after-contacts-field>
        <span class="-mt-2 block text-right">
          <a
            href="#"
            @click.prevent="contactBeingCreated = true"
            class="link text-sm"
          >
            + {{ $t('contacts::contact.create') }}
          </a>
        </span>
      </template>
    </FieldsGenerator>

    <template #modal-ok v-if="withExtendedSubmitButtons">
      <IDropdownButtonGroup
        type="submit"
        placement="top-end"
        :disabled="form.busy"
        :loading="form.busy"
        :text="$t('core::app.create')"
      >
        <IDropdownItem
          @click="createAndAddAnother"
          :text="$t('core::app.create_and_add_another')"
        />
        <IDropdownItem
          v-if="goToList"
          @click="createAndGoToList"
          :text="$t('core::app.create_and_go_to_list')"
        />
      </IDropdownButtonGroup>
    </template>

    <CreateDealModal
      v-model:visible="dealBeingCreated"
      :overlay="false"
      @created="
        fields.mergeValue('deals', $event.deal), (dealBeingCreated = false)
      "
    />

    <CreateContactModal
      v-model:visible="contactBeingCreated"
      :overlay="false"
      @created="
        fields.mergeValue('contacts', $event.contact),
          (contactBeingCreated = false)
      "
      @restored="
        fields.mergeValue('contacts', $event), (contactBeingCreated = false)
      "
    />

    <CreateCompanyModal
      v-model:visible="companyBeingCreated"
      :overlay="false"
      @created="
        fields.mergeValue('companies', $event.company),
          (companyBeingCreated = false)
      "
      @restored="
        fields.mergeValue('companies', $event), (companyBeingCreated = false)
      "
    />
  </ISlideover>
</template>

<script setup>
import { ref } from 'vue'
import { whenever } from '@vueuse/core'
import { computedWithControl } from '@vueuse/shared'
import { useResourceFields } from '~/Core/resources/js/composables/useResourceFields'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useFieldsForm } from '~/Core/resources/js/components/Fields/useFieldsForm'
import cloneDeep from 'lodash/cloneDeep'
import map from 'lodash/map'

const emit = defineEmits([
  'created',
  'modal-hidden',
  'modal-shown',
  'update:visible',
])

const props = defineProps({
  visible: { type: Boolean, default: true },
  goToList: { type: Boolean, default: true },
  redirectToView: { type: Boolean, default: false },
  withExtendedSubmitButtons: { type: Boolean, default: false },
  title: String,

  title: {},
  note: {},
  description: {},
  activityTypeId: {},
  contacts: {},
  companies: {},
  deals: {},
  dueDate: {},
  endDate: {},
  reminderMinutesBefore: {},
})

const { t } = useI18n()
const router = useRouter()

const { fields, getCreateFields } = useResourceFields()

const { form } = useFieldsForm(fields)

const dealBeingCreated = ref(false)
const contactBeingCreated = ref(false)
const companyBeingCreated = ref(false)

// Provide initial focus element as the modal can be nested and it's not
// finding an element for some reason when the second modal is closed
// showing error "There are no focusable elements inside the <FocusTrap />"
const modalCloseElement = computedWithControl(
  () => null,
  () => document.querySelector('#modalClose-createActivityModal')
)

whenever(() => props.visible, prepareComponent, { immediate: true })

function create() {
  request().then(createdHandler)
}

function createAndAddAnother() {
  request('create-another').then(data => {
    form.reset()
    createdHandler(data)
  })
}

function createAndGoToList() {
  request('go-to-list').then(createdHandler)
}

async function request(actionType = null) {
  try {
    let activity = await form.hydrate().post('/activities')

    let payload = {
      activity: activity,
      isRegularAction: actionType === null,
      action: actionType,
    }

    emit('created', payload)

    Innoclapps.success(t('activities::activity.created'))

    return payload
  } catch (e) {
    if (e.isValidationError()) {
      Innoclapps.error(t('core::app.form_validation_failed'), 3000)
    }

    return Promise.reject(e)
  }
}

function handleModalShownEvent() {
  emit('modal-shown')

  modalCloseElement.trigger()
}

function handleModalHiddenEvent() {
  emit('modal-hidden')

  fields.value.set([])
}

function createdHandler(data) {
  data.indexRoute = { name: 'activity-index' }

  if (data.action === 'go-to-list') {
    return router.push(data.indexRoute)
  }

  if (data.action === 'create-another') return

  // Not used yet as the activity has no view, it's an alias of EDIT
  if (props.redirectToView) {
    let activity = data.activity
    router.activity = activity

    router.push({
      name: 'view-activity',
      params: {
        id: activity.id,
      },
    })
  }
}

async function prepareComponent() {
  const createFields = await getCreateFields(
    Innoclapps.config('fields.groups.activities')
  )

  fields.value.set(
    map(cloneDeep(createFields), field => {
      if (
        [
          'contacts',
          'companies',
          'deals',
          'title',
          'note',
          'description',
        ].indexOf(field.attribute) > -1
      ) {
        field.value = props[field.attribute]
      } else if (
        field.attribute === 'activity_type_id' &&
        props.activityTypeId
      ) {
        field.value = props.activityTypeId
      } else if (field.attribute === 'due_date' && props.dueDate) {
        field.value = props.dueDate // object
      } else if (field.attribute === 'end_date' && props.endDate) {
        field.value = props.endDate // object
      } else if (
        field.attribute === 'reminder_minutes_before' &&
        props.reminderMinutesBefore
      ) {
        field.value = props.reminderMinutesBefore
      }

      return field
    })
  )
}
</script>
