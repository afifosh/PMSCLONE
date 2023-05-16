<template>
  <FieldFormGroup
    :field="field"
    :form-id="formId"
    :field-id="fieldId"
    class="relative"
  >
    <IFormLabel
      :for="fieldId + '-due-date'"
      class="mb-1 block sm:hidden"
      :label="$t('activities::activity.due_date')"
    />
    <div class="flex items-center space-x-1">
      <DatePicker
        v-model="dueDate"
        :id="fieldId + '-due-date'"
        :name="field.attribute"
        :disabled="isReadonly"
        :with-icon="false"
        :required="field.isRequired"
        v-bind="field.attributes"
      />
      <IFormInputDropdown
        @blur="maybeSetEndTimeToEmpty"
        @cleared="maybeSetEndTimeToEmpty"
        :items="inputDropdownItems"
        :full="false"
        :placeholder="timeFormatForMoment"
        :input-id="fieldId + '-due-time'"
        max-height="300px"
        :class="{
          'border-danger-500 ring-danger-500 focus:border-danger-500 focus:ring-danger-500':
            showDueTimeWarning,
        }"
        v-model="dueTime"
      />
      <div
        class="absolute -right-3 hidden text-neutral-900 dark:text-neutral-300 md:block"
      >
        -
      </div>
    </div>
  </FieldFormGroup>
</template>
<script setup>
import { ref, toRef, computed, watch, nextTick, onMounted } from 'vue'
import FieldFormGroup from '~/Core/resources/js/components/Fields/FieldFormGroup.vue'
import propsDefinition from '~/Core/resources/js/components/Fields/props'
import { useField } from '~/Core/resources/js/components/Fields/useField'
import isEqual from 'lodash/isEqual'
import { timelineLabels } from '@/utils'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useGlobalEventListener } from '~/Core/resources/js/composables/useGlobalEventListener'

const props = defineProps(propsDefinition)

const { appMoment, currentTimeFormat, dateFromAppTimezone, dateToAppTimezone } =
  useDates()

const endDate = ref(appMoment().format('YYYY-MM-DD'))
const endTime = ref('')
const dueDate = ref('')
const dueTime = ref('')
const settingDueDateInitialValue = ref(false)

const value = ref('')

watch(dueDate, (newVal, oldVal) => {
  Innoclapps.$emit('due-date-changed', {
    newVal: newVal,
    oldVal: oldVal,
    isInitialValue: settingDueDateInitialValue.value,
  })
})

watch(dueTime, (newVal, oldVal) => {
  Innoclapps.$emit('due-time-changed', {
    newVal: newVal,
    oldVal: oldVal,
  })

  if (!endTime.value || endTime.value === dueDate.value) {
    return
  }

  let newDueDate = utcMomentInstanceFromDateAndDropdownTime(
    dueDate.value,
    newVal
  )
  let currentEndDate = utcMomentInstanceFromDateAndDropdownTime(
    endDate.value,
    endTime.value
  )
  let oldDueDate = utcMomentInstanceFromDateAndDropdownTime(
    dueDate.value,
    oldVal
  )

  invokeUpdateEndTimeValueEvent(
    dateFromAppTimezone(
      newDueDate
        .add(currentEndDate.diff(oldDueDate, 'minutes'), 'minutes')
        .format('YYYY-MM-DD HH:mm:ss'),
      timeFormatForMoment.value
    )
  )
})

const isDirty = computed(() => !isEqual(realInitialValue.value, getValues()))

const showDueTimeWarning = computed(() => endTime.value && !dueTime.value)

const timeFormatForMoment = computed(() =>
  moment().PHPconvertFormat(currentTimeFormat.value)
)

const inputDropdownItems = computed(() =>
  timelineLabels('00:00', 15, 'm', timeFormatForMoment.value)
)

/**
 * Create UTC moment instance from the given date and dropdown time (already formatted)
 *
 * @return {Moment}
 */
function utcMomentInstanceFromDateAndDropdownTime(date, time) {
  return moment.utc(
    dateToAppTimezone(
      moment(
        date +
          ' ' +
          moment(
            date + ' ' + time,
            'YYYY-MM-DD ' + timeFormatForMoment.value
          ).format('HH:mm')
      ).format('YYYY-MM-DD HH:mm:ss')
    )
  )
}

/**
 * Invoke update end time event
 *
 * @param  {mixed} value
 *
 * @return {Void}
 */
function invokeUpdateEndTimeValueEvent(value) {
  Innoclapps.$emit('update-end-time', value)
}

/**
 * Provide a function that fills a passed form object with the
 *
 * field's internal value attribute
 */
function fill(form) {
  const values = getValues()

  form.fill('due_date', values.date)
  form.fill('due_time', values.time || null)
}

/**
 * Get the actual field values for storage in UTC format
 *
 * @return {Object}
 */
function getValues() {
  if (dueTime.value) {
    const UTCInstance = utcMomentInstanceFromDateAndDropdownTime(
      dueDate.value,
      dueTime.value
    )

    return {
      date: UTCInstance.format('YYYY-MM-DD'),
      time: UTCInstance.format('HH:mm'),
    }
  }

  return {
    date: dueDate.value,
    time: '',
  }
}

/**
 * Set the dates
 *
 * @param {Object|null} value
 */
function setDates(value) {
  if (!value) {
    return
  }

  if (value.time) {
    dueDate.value = dateFromAppTimezone(
      value.date + ' ' + value.time,
      'YYYY-MM-DD'
    )
    dueTime.value = dateFromAppTimezone(
      value.date + ' ' + value.time,
      timeFormatForMoment.value
    )
  } else {
    dueDate.value = value.date
    endDate.value = ''
  }
}

/**
 * Handle field value changed
 *
 * @param  {String} newValue
 *
 * @return {Void}
 */
function handleChange(newValue) {
  value.value = newValue
  realInitialValue.value = newValue
  setDates(newValue)
}

/*
 * Set the initial value for the field
 */
function setInitialValue() {
  settingDueDateInitialValue.value = true

  if (!props.field.value) {
    // https://stackoverflow.com/questions/17691202/round-up-round-down-a-momentjs-moment-to-nearest-minute
    const UTCInstance = appMoment().add(1, 'hour').startOf('hour')

    dueTime.value = dateFromAppTimezone(
      UTCInstance.format('YYYY-MM-DD HH:mm:ss'),
      timeFormatForMoment.value
    )

    dueDate.value = dateFromAppTimezone(
      UTCInstance.format('YYYY-MM-DD HH:mm:ss'),
      'YYYY-MM-DD'
    )

    value.value = {
      date: dueDate.value,
      time: UTCInstance.format('HH:mm'), // utc
    }

    nextTick(() => (settingDueDateInitialValue.value = false))

    return
  }

  value.value = props.field.value

  setDates(value.value)

  nextTick(() => (settingDueDateInitialValue.value = false))
}

/**
 * If we don't have due time we will set the end time to empty
 *
 * @return {Void}
 */
async function maybeSetEndTimeToEmpty() {
  await nextTick()

  if (!dueTime.value && endTime.value) {
    invokeUpdateEndTimeValueEvent('')
  }
}

const { field, fieldId, isReadonly, realInitialValue, initialize } = useField(
  value,
  toRef(props, 'field'),
  props.formId,
  toRef(props, 'isFloating'),
  {
    isDirty,
    setInitialValue,
    handleChange,
    fill,
  }
)

onMounted(initialize)

useGlobalEventListener(
  'end-time-changed',
  event => (endTime.value = event.newVal)
)

useGlobalEventListener(
  'end-date-changed',
  event => (endDate.value = event.newVal)
)
</script>

<style>
input[name='due_date-due-time'] {
  width: 116px !important;
}
</style>
