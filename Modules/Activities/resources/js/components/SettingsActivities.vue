<template>
  <div>
    <ICard
      no-body
      :header="$t('activities::activity.activities')"
      class="mb-3"
      :overlay="!componentReady"
    >
      <ul class="divide-y divide-neutral-200 dark:divide-neutral-700">
        <li class="px-4 py-4 sm:px-6">
          <div
            class="space-x-0 space-y-3 md:flex md:items-center md:justify-between md:space-y-0 lg:space-x-3"
          >
            <div>
              <h5
                class="font-medium leading-relaxed text-neutral-700 dark:text-neutral-100"
                v-t="'activities::activity.settings.send_contact_email'"
              />
              <p
                class="text-sm text-neutral-600 dark:text-neutral-300"
                v-t="'activities::activity.settings.send_contact_email_info'"
              />
            </div>
            <div>
              <IFormToggle
                :value="true"
                :unchecked-value="false"
                @change="submit"
                v-model="form.send_contact_attends_to_activity_mail"
              />
            </div>
          </div>
        </li>
        <li class="px-4 py-4 sm:px-6">
          <IFormGroup
            :label="$t('activities::activity.type.default_type')"
            class="mb-0"
            label-for="default_activity_type"
          >
            <ICustomSelect
              input-id="default_activity_type"
              v-model="defaultType"
              class="xl:w-1/3"
              :clearable="false"
              @option:selected="handleActivityTypeInputEvent"
              label="name"
              :options="types"
            >
            </ICustomSelect>
          </IFormGroup>
        </li>
      </ul>
    </ICard>
    <ActivityTypeIndex></ActivityTypeIndex>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import { watchOnce } from '@vueuse/core'
import ActivityTypeIndex from '../views/ActivityTypeIndex.vue'
import { useSettings } from '~/Core/resources/js/views/Settings/useSettings'
import { useApp } from '~/Core/resources/js/composables/useApp'
import { useActivityTypes } from '../composables/useActivityTypes'

const { resetStoreState } = useApp()
const { form, submit, isReady: componentReady } = useSettings()

const defaultType = ref(null)

const { typesByName: types } = useActivityTypes()

function handleActivityTypeInputEvent(e) {
  form.default_activity_type = e.id
  submit(resetStoreState)
}

watchOnce(componentReady, function (newVal, oldVal) {
  defaultType.value = types.value.find(
    type => type.id == form.default_activity_type
  )
})
</script>
