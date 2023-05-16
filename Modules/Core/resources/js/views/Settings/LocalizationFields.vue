<template>
  <div>
    <IFormGroup
      v-if="withTimezoneField"
      :label="$t('core::app.timezone')"
      label-for="timezone"
    >
      <FormTimezone
        :modelValue="form.timezone"
        @update:modelValue="$emit('update:timezone', $event)"
      />
      <IFormError v-text="form.getError('timezone')" />
    </IFormGroup>
    <IFormGroup
      v-if="withLocaleField"
      :label="$t('core::app.locale')"
      label-for="locale"
    >
      <ICustomSelect
        input-id="locale"
        :modelValue="form.locale"
        @update:modelValue="$emit('update:locale', $event)"
        :clearable="false"
        :options="locales"
      >
      </ICustomSelect>
      <IFormError v-text="form.getError('locale')" />
    </IFormGroup>
    <IFormGroup
      :label="$t('core::settings.date_format')"
      label-for="date_format"
    >
      <FormDateFormat
        :modelValue="form.date_format"
        @update:modelValue="$emit('update:dateFormat', $event)"
      />
      <IFormError v-text="form.getError('date_format')" />
    </IFormGroup>
    <IFormGroup
      :label="$t('core::settings.time_format')"
      label-for="time_format"
    >
      <FormTimeFormat
        :modelValue="form.time_format"
        @update:modelValue="$emit('update:timeFormat', $event)"
      />
      <IFormError v-text="form.getError('time_format')" />
    </IFormGroup>
    <IFormGroup
      :label="$t('core::settings.first_day_of_week')"
      label-for="first_day_of_week"
    >
      <!-- http://chartsbin.com/view/41671 -->
      <FormWeekDaysSelect
        :modelValue="form.first_day_of_week"
        @update:modelValue="$emit('update:firstDayOfWeek', $event)"
        :only="[1, 6, 0]"
      />
      <IFormError v-text="form.getError('first_day_of_week')" />
    </IFormGroup>
  </div>
</template>
<script setup>
import { computed } from 'vue'
import FormTimezone from '~/Core/resources/js/components/FormTimezone.vue'
import FormDateFormat from '~/Core/resources/js/components/FormDateFormat.vue'
import FormTimeFormat from '~/Core/resources/js/components/FormTimeFormat.vue'
import FormWeekDaysSelect from '~/Core/resources/js/components/FormWeekDaysSelect.vue'
import { useApp } from '~/Core/resources/js/composables/useApp'

const emit = defineEmits([
  'update:firstDayOfWeek',
  'update:timeFormat',
  'update:dateFormat',
  'update:locale',
  'update:timezone',
])

const props = defineProps({
  firstDayOfWeek: {},
  timeFormat: {},
  dateFormat: {},
  locale: {},
  timezone: {},
  form: { required: true, type: Object },
  exclude: { type: Array, default: () => [] },
})

const { locales } = useApp()

const withTimezoneField = computed(
  () => props.exclude.indexOf('timezone') === -1
)

const withLocaleField = computed(() => props.exclude.indexOf('locale') === -1)
</script>
