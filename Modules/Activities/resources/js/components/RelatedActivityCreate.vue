<template>
  <ICard
    tag="form"
    @submit.prevent="create"
    method="POST"
    :overlay="fields.isEmpty()"
  >
    <FieldsGenerator
      focus-first
      :form-id="form.formId"
      :fields="fields"
      :via-resource="viaResource"
      :via-resource-id="record.id"
      view="create"
    />

    <template #footer>
      <div class="flex w-full flex-wrap items-center justify-between sm:w-auto">
        <div>
          <AssociationsPopover
            :resource-name="viaResource"
            :associateable="record"
            v-model="form.associations"
          />
        </div>
        <div
          class="mt-sm-0 mt-2 flex w-full flex-col sm:w-auto sm:flex-row sm:items-center sm:justify-end sm:space-x-2"
        >
          <IFormToggle
            class="mb-4 mr-4 pr-4 sm:mb-0 sm:border-r sm:border-neutral-200 sm:dark:border-neutral-700"
            :label="$t('activities::activity.mark_as_completed')"
            v-model="form.is_completed"
          />
          <IButton
            class="mb-2 sm:mb-0"
            variant="white"
            @click="$emit('cancel')"
            size="sm"
            :text="$t('core::app.cancel')"
          />
          <IButton
            type="submit"
            size="sm"
            :disabled="form.busy"
            :text="$t('activities::activity.add')"
          />
        </div>
      </div>
    </template>

    <IAlert
      :show="form.recentlySuccessful"
      class="border border-success-300"
      variant="success"
    >
      {{ $t('activities::activity.created') }}
    </IAlert>
  </ICard>
</template>

<script setup>
import AssociationsPopover from '~/Core/resources/js/components/AssociationsPopover.vue'
import { useResourceFields } from '~/Core/resources/js/composables/useResourceFields'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useFieldsForm } from '~/Core/resources/js/components/Fields/useFieldsForm'
import { useI18n } from 'vue-i18n'

const resourceName = Innoclapps.config('resources.activities.name')

const emit = defineEmits(['cancel'])

const props = defineProps({
  viaResource: { type: String, required: true },
})

const { t } = useI18n()

const {
  record,
  incrementResourceRecordCount,
  addResourceRecordHasManyRelationship,
} = useRecordStore()

const { fields, getCreateFields } = useResourceFields()

const { form } = useFieldsForm(
  fields,
  {
    is_completed: false,
    associations: {
      [props.viaResource]: [record.value.id],
    },
  },
  {
    resetOnSuccess: true,
  }
)

function prepareComponent() {
  getCreateFields(resourceName, {
    viaResource: props.viaResource,
    viaResourceId: record.value.id,
  }).then(createFields => fields.value.set(createFields))
}

/**
 * Store the activity in storage
 *
 * @return {Void}
 */
function create() {
  form
    .withQueryString({
      via_resource: props.viaResource,
      via_resource_id: record.value.id,
    })
    .hydrate()
    .post('/activities')
    .then(activity => {
      Innoclapps.success(t('activities::activity.created'))
      incrementResourceRecordCount('incomplete_activities_for_user_count')
      addResourceRecordHasManyRelationship(activity, 'activities')
    })
}

prepareComponent()
</script>
