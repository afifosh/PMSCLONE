<template>
  <ISlideover
    @shown="handleModalShownEvent"
    @hidden="handleModalHiddenEvent"
    @submit="createUsing ? createUsing(create) : create()"
    @update:visible="$emit('update:visible', $event)"
    :visible="visible"
    :title="title || $t('contacts::contact.create')"
    :ok-title="$t('core::app.create')"
    :ok-disabled="form.busy"
    :cancel-title="$t('core::app.cancel')"
    static-backdrop
    :initial-focus="modalCloseElement"
    id="createContactModal"
    form
  >
    <FieldsPlaceholder v-if="fields.isEmpty()" />

    <slot name="top" :isReady="fields.isNotEmpty()"></slot>

    <div v-show="fieldsVisible">
      <FieldsGenerator
        focus-first
        :form-id="form.formId"
        :fields="fields"
        view="create"
        :is-floating="true"
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

        <template #after-email-field v-if="trashedContactByEmail !== null">
          <IAlert
            dismissible
            class="mb-3"
            @dismissed="
              ;(recentlyRestored.byEmail = false),
                (trashedContactByEmail = null)
            "
          >
            {{ $t('contacts::contact.exists_in_trash_by_email') }}

            <div class="mt-4">
              <div class="-mx-2 -my-1.5 flex">
                <IButtonMinimal
                  v-show="!recentlyRestored.byEmail"
                  variant="info"
                  @click="restoreTrashed(trashedContactByEmail.id, 'byEmail')"
                  :text="$t('core::app.soft_deletes.restore')"
                />
                <IButtonMinimal
                  v-show="recentlyRestored.byEmail"
                  variant="info"
                  :to="{
                    name: 'view-contact',
                    params: { id: trashedContactByEmail.id },
                  }"
                  :text="$t('core::app.view_record')"
                />
              </div>
            </div>
          </IAlert>
        </template>
        <template #after-phones-field v-if="trashedContactsByPhone.length > 0">
          <IAlert
            v-for="(contact, index) in trashedContactsByPhone"
            :key="contact.id"
            dismissible
            class="mb-3"
            @dismissed="
              ;(recentlyRestored.byPhone[contact.id] = false),
                (trashedContactsByPhone[index] = null)
            "
          >
            {{
              $t('contacts::contact.exists_in_trash_by_phone', {
                contact: contact.display_name,
                phone_numbers: contact.phones
                  .map(phone => phone.number)
                  .join(','),
              })
            }}

            <div class="mt-4">
              <div class="-mx-2 -my-1.5 flex">
                <IButtonMinimal
                  v-show="!recentlyRestored.byPhone[contact.id]"
                  variant="info"
                  @click="restoreTrashed(contact.id, 'byPhone')"
                  :text="$t('core::app.soft_deletes.restore')"
                />
                <IButtonMinimal
                  v-show="recentlyRestored.byPhone[contact.id]"
                  variant="info"
                  :to="{
                    name: 'view-contact',
                    params: { id: contact.id },
                  }"
                  :text="$t('core::app.view_record')"
                />
              </div>
            </div>
          </IAlert>
        </template>
      </FieldsGenerator>
    </div>

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
          v-show="goToList"
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
import { ref, shallowRef } from 'vue'
import { whenever } from '@vueuse/core'
import { watchDebounced } from '@vueuse/shared'
import { useResourceFields } from '~/Core/resources/js/composables/useResourceFields'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useFieldsForm } from '~/Core/resources/js/components/Fields/useFieldsForm'
import { computedWithControl } from '@vueuse/shared'

const emit = defineEmits([
  'created',
  'restored',
  'modal-shown',
  'modal-hidden',
  'update:visible',
  'ready',
])

const props = defineProps({
  visible: { type: Boolean, default: true },
  goToList: { type: Boolean, default: true },
  redirectToView: { type: Boolean, default: false },
  createUsing: Function,
  withExtendedSubmitButtons: { type: Boolean, default: false },
  fieldsVisible: { type: Boolean, default: true },
  title: String,

  firstName: String,
  lastName: String,
  email: String,

  associations: Object,

  companies: Array,
  deals: Array,
})

const { t } = useI18n()
const router = useRouter()

const dealBeingCreated = ref(false)
const companyBeingCreated = ref(false)

const emailField = ref({})
const phoneField = ref({})

const trashedContactByEmail = shallowRef(null)
const trashedContactsByPhone = ref([])

const recentlyRestored = ref({
  byEmail: false,
  byPhone: {},
})

const { fields, getCreateFields } = useResourceFields()

const { form } = useFieldsForm(fields, {
  avatar: null,
})

// Provide initial focus element as the modal can be nested and it's not
// finding an element for some reason when the second modal is closed
// showing error "There are no focusable elements inside the <FocusTrap />"
const modalCloseElement = computedWithControl(
  () => null,
  () => document.querySelector('#modalClose-createContactModal')
)

whenever(() => props.visible, prepareComponent, { immediate: true })

watchDebounced(
  () => emailField.value?.currentValue,
  newVal => {
    if (!newVal) {
      trashedContactByEmail.value = null
      return
    }

    Innoclapps.request()
      .get(`/trashed/contacts/search`, {
        params: {
          q: newVal,
          search_fields: 'email:=',
        },
      })
      .then(({ data: contacts }) => {
        trashedContactByEmail.value = contacts.length > 0 ? contacts[0] : null
      })
  },
  { debounce: 500 }
)

watchDebounced(
  () => phoneField.value?.currentValue,
  newVal => {
    if (!newVal) return

    const numbers = newVal
      .filter(
        phone =>
          !phone.number ||
          !(
            phoneField.value.callingPrefix &&
            phoneField.value.callingPrefix.trim() === phone.number
          )
      )
      .map(phone => phone.number)

    if (numbers.length === 0) {
      trashedContactsByPhone.value = []
      return
    }

    Innoclapps.request()
      .get(`/trashed/contacts/search`, {
        params: {
          q: numbers.join(','),
          search_fields: 'phones.number:in',
        },
      })
      .then(({ data: contacts }) => {
        trashedContactsByPhone.value = contacts
      })
  },
  { debounce: 500, deep: true }
)

function createdHandler(data) {
  data.indexRoute = { name: 'contact-index' }

  if (data.action === 'go-to-list') {
    return router.push(data.indexRoute)
  }

  if (data.action === 'create-another') return

  if (props.redirectToView) {
    let contact = data.contact
    router.contact = contact

    router.push({
      name: 'view-contact',
      params: {
        id: contact.id,
      },
    })
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

function restoreTrashed(id, type) {
  Innoclapps.request()
    .post(`/trashed/contacts/${id}`)
    .then(({ data }) => {
      if (typeof recentlyRestored.value[type] === 'object') {
        recentlyRestored.value[type][data.id] = true
      } else {
        recentlyRestored.value[type] = true
      }

      emit('restored', data)
    })
}

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
  if (props.associations) {
    form.fill(props.associations)
  }

  let contact = await form
    .hydrate()
    .post('/contacts')
    .catch(e => {
      if (e.isValidationError()) {
        Innoclapps.error(t('core::app.form_validation_failed'), 3000)
      }
      return Promise.reject(e)
    })

  let payload = {
    contact: contact,
    isRegularAction: actionType === null,
    action: actionType,
  }

  emit('created', payload)

  Innoclapps.success(t('core::resource.created'))

  return payload
}

async function prepareComponent() {
  let createFields = await getCreateFields(
    Innoclapps.config('fields.groups.contacts')
  )

  fields.value.set(createFields)

  // From props, same attribute name and prop name
  ;['companies', 'deals'].forEach(attribute => {
    if (props[attribute]) {
      fields.value.updateValue(attribute, props[attribute])
    }
  })

  emailField.value = fields.value.find('email')
  phoneField.value = fields.value.find('phones')

  if (props.email) {
    fields.value.updateValue('email', props.email)
  }

  if (props.firstName) {
    fields.value.updateValue('first_name', props.firstName)
  }

  if (props.lastName) {
    fields.value.updateValue('last_name', props.lastName)
  }

  emit('ready', fields)
}
</script>
