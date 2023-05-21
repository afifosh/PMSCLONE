<template>
  <div
    v-show="
      componentReady && (recordForm.isDirty || recordForm.recentlySuccessful)
    "
    class="sticky top-2 z-10 mt-5 block overflow-hidden rounded-md bg-white px-6 py-4 shadow dark:bg-neutral-700 md:hidden"
  >
    <div class="flex items-center">
      <IButton
        size="sm"
        @click="update"
        class="relative z-10 px-5"
        :loading="recordForm.busy"
        :disabled="
          !recordForm.isDirty ||
          recordForm.busy ||
          $gate.denies('update', company)
        "
        :text="$t('core::app.save')"
      />
      <IActionMessage
        :message="$t('core::app.saved')"
        class="relative z-10 ml-2"
        v-show="recordForm.recentlySuccessful"
      />
    </div>
  </div>
  <form @submit.prevent="update" novalidate="true">
    <IOverlay
      :show="!componentReady"
      :class="{
        'rounded-lg bg-white p-6 shadow dark:bg-neutral-900': !componentReady,
      }"
    >
      <IAlert
        v-if="recordForm.errors.any()"
        variant="warning"
        class="mb-2 border border-warning-200"
      >
        {{ $t('core::app.form_validation_failed') }}
      </IAlert>
      <div
        v-show="componentReady"
        class="overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900"
      >
        <div class="flex items-center justify-between px-6 pt-5">
          <h2
            class="font-medium text-neutral-800 dark:text-white"
            v-t="'core::app.record_view.sections.details'"
          />

          <div class="flex items-center space-x-3">
            <IButtonIcon
              v-if="$gate.isSuperAdmin()"
              size="sm"
              v-i-tooltip="$t('core::fields.manage')"
              :to="{
                name: 'resource-fields',
                params: { resourceName: resourceName },
                query: { view: fieldsViewName },
              }"
              icon="Cog"
            />

            <IButtonIcon
              v-if="hasCollapsibleFields"
              class="mt-px"
              :icon="fieldsCollapsed ? 'ChevronDown' : 'ChevronUp'"
              v-i-tooltip="
                fieldsCollapsed
                  ? $t('core::fields.more')
                  : $t('core::fields.less')
              "
              @click="fieldsCollapsed = !fieldsCollapsed"
            />
          </div>
        </div>
        <FieldsGenerator
          v-if="componentReady"
          :class="['overflow-y-auto p-6', { 'h-56': !fieldsHeight }]"
          resizeable
          :collapsed="fieldsCollapsed"
          :resource-name="resourceName"
          :fields="fields"
          :form-id="recordForm.formId"
          :view="fieldsViewName"
        />
        <div v-show="recordForm.isDirty || recordForm.recentlySuccessful">
          <div
            class="hidden items-center justify-end bg-neutral-50 px-6 py-3 text-right dark:bg-neutral-700 md:flex"
          >
            <IActionMessage
              v-show="recordForm.recentlySuccessful"
              :message="$t('core::app.saved')"
              class="mr-2 hidden md:block"
            />
            <IButton
              type="submit"
              size="sm"
              :loading="recordForm.busy"
              :disabled="
                !recordForm.isDirty ||
                recordForm.busy ||
                $gate.denies('update', company)
              "
              :text="$t('core::app.save')"
            />
          </div>
        </div>
      </div>
    </IOverlay>
  </form>
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import { ref } from 'vue'
import { useResourceUpdate } from '~/Core/resources/js/composables/useResourceUpdate'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  company: { required: true, type: Object },
})

const { t } = useI18n()

const resourceName = Innoclapps.config('resources.companies.name')
const fieldsViewName = Innoclapps.config('fields.views.detail')
const fieldsHeight = ref(Innoclapps.config('company_fields_height'))
const fieldsCollapsed = ref(true)

const {
  isReady: componentReady,
  form: recordForm,
  fields,
  hasCollapsibleFields,
  setFormValues,
  update,
  boot,
} = useResourceUpdate(resourceName)

const { setRecord } = useRecordStore()

boot(props.company, {
  view: fieldsViewName,
  onAfterUpdate: record => {
    setRecord(record)
    Innoclapps.success(t('core::resource.updated'))
  },
})

defineExpose({ setFormValues })
</script>
