<template>
  <form @submit.prevent="update" method="POST">
    <ICard :overlay="!componentReady">
      <FieldsGenerator
        :form-id="recordForm.formId"
        view="update"
        :via-resource="viaResource"
        :via-resource-id="record.id"
        :fields="fields"
      />
      <template #footer>
        <div
          class="flex w-full flex-col sm:w-auto sm:flex-row sm:items-center sm:justify-end"
        >
          <IFormToggle
            class="mb-4 mr-4 pr-4 sm:mb-0 sm:border-r sm:border-neutral-200 sm:dark:border-neutral-700"
            :label="$t('activities::activity.mark_as_completed')"
            v-model="recordForm.is_completed"
          />
          <IButton
            class="mb-2 ml-0 sm:mb-0 sm:mr-2"
            variant="white"
            size="sm"
            :text="$t('core::app.cancel')"
            @click="$emit('cancelled', $event)"
          />
          <IButton
            type="submit"
            variant="primary"
            @click="update"
            size="sm"
            :disabled="recordForm.busy"
            :text="$t('core::app.save')"
          />
        </div>
      </template>
    </ICard>
  </form>
</template>
<script setup>
import { onBeforeMount } from 'vue'
import { useResourceUpdate } from '~/Core/resources/js/composables/useResourceUpdate'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useResourceFields } from '~/Core/resources/js/composables/useResourceFields'
import { useI18n } from 'vue-i18n'

const emit = defineEmits(['cancelled', 'updated'])

const props = defineProps({
  viaResource: { type: String, required: true },
  activityId: { type: Number, required: true },
})

const resourceName = Innoclapps.config('resources.activities.name')

const { t } = useI18n()

const { getUpdateFields } = useResourceFields()

const {
  isReady: componentReady,
  form: recordForm,
  fields,
  boot,
  update,
} = useResourceUpdate(resourceName)

const {
  record,
  updateResourceRecordHasManyRelationship,
  incrementResourceRecordCount,
  decrementResourceRecordCount,
} = useRecordStore()

function handleActivityUpdated(activity) {
  // For the mark as completed toggle
  if (activity.is_completed !== record.value.is_completed) {
    if (activity.is_completed) {
      decrementResourceRecordCount('incomplete_activities_for_user_count')
    } else {
      incrementResourceRecordCount('incomplete_activities_for_user_count')
    }
  }

  updateResourceRecordHasManyRelationship(activity, 'activities')

  emit('updated', activity)
}

onBeforeMount(async () => {
  const { data: activity } = await Innoclapps.request().get(
    `/activities/${props.activityId}`
  )

  boot(activity, {
    onBeforeUpdate: form =>
      form.withQueryString({
        via_resource: props.viaResource,
        via_resource_id: record.value.id,
      }),
    fields: () =>
      getUpdateFields(resourceName, props.activityId, {
        viaResource: props.viaResource,
        viaResourceId: record.value.id,
      }),
    onAfterUpdate: record => {
      handleActivityUpdated(record)
      Innoclapps.success(t('core::resource.updated'))
    },
    onReady: record => {
      // For checkbox mark as completed
      recordForm.set('is_completed', record.is_completed)

      fields.value.update('guests', {
        activity: record,
      })
    },
  })
})
</script>
