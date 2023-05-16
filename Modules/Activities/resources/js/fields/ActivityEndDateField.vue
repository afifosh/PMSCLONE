<template>
  <FieldFormGroup :field="field" :form-id="formId" :field-id="fieldId">
    <IFormLabel
      :for="fieldId + '-end-date'"
      class="mb-1 block sm:hidden"
      :label="$t('activities::activity.end_date')"
    />
    <div class="flex items-center space-x-1">
      <IFormInputDropdown
        :items="inputDropdownItems"
        :input-id="fieldId + '-end-time'"
        :full="false"
        v-model="endTime"
        :placeholder="timeFormatForMoment"
        :disabled="!dueTime"
        max-height="300px"
        :class="{
          'border-danger-500 ring-danger-500 focus:border-danger-500 focus:ring-danger-500':
            showEndTimeWarning,
        }"
        @shown="handleTimeIsShown"
      />
      <DatePicker
        v-model="endDate"
        :required="field.isRequired"
        :min-date="dueDate"
        :with-icon="false"
        :id="fieldId + '-end-date'"
        :name="field.attribute"
        :disabled="isReadonly"
        v-bind="field.attributes"
      />
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

const value = ref('')

watch(endTime, (newVal, oldVal) => {
  Innoclapps.$emit('end-time-changed', {
    newVal: newVal,
    oldVal: oldVal,
  })
})
watch(endDate, (newVal, oldVal) => {
  Innoclapps.$emit('end-date-changed', {
    newVal: newVal,
    oldVal: oldVal,
  })
})

const isDirty = computed(() => !isEqual(realInitialValue.value, getValues()))

const timeFormatForMoment = computed(() =>
  moment().PHPconvertFormat(currentTimeFormat.value)
)

const showEndTimeWarning = computed(() => {
  if (!endTime.value && dueTime.value && endDate.value > dueDate.value) {
    return true
  }
})

const inputDropdownItems = computed(() => {
  if (!dueTime.value || endDate.value > dueDate.value) {
    return timelineLabels('00:00', 15, 'm', timeFormatForMoment.value, 23)
  }

  const startIn24HourFormat = moment(
    dueDate.value + ' ' + dueTime.value,
    'YYYY-MM-DD ' + timeFormatForMoment.value
  ).format('HH:mm')

  return timelineLabels(
    startIn24HourFormat,
    15,
    'm',
    timeFormatForMoment.value,
    23
  )
})

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
 * Provide a function that fills a passed form object with the
 *
 * field's internal value attribute
 */
function fill(form) {
  const values = getValues()
  form.fill('end_date', values.date)
  form.fill('end_time', values.time || null)
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
    endDate.value = dateFromAppTimezone(
      value.date + ' ' + value.time,
      'YYYY-MM-DD'
    )
    endTime.value = dateFromAppTimezone(
      value.date + ' ' + value.time,
      timeFormatForMoment.value
    )

    nextTick(() => {
      if (endTime.value === dueTime.value && endDate.value === dueDate.value) {
        endTime.value = ''
      }
    })
  } else {
    endDate.value = value.date
    endTime.value = ''
  }
}

/**
 * Time shown event
 *
 * @return {Void}
 */
function handleTimeIsShown() {
  if (!endTime.value) {
    endTime.value = dueTime.value
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
  realInitialValue.value = value.value

  setDates(newValue)
}

/*
 * Set the initial value for the field
 */
function setInitialValue() {
  if (!props.field.value) {
    value.value = {
      date: endDate.value,
      time: endTime.value,
    }
    return
  }

  value.value = props.field.value
  setDates(value.value)
}

/**
 * Get the actual field values for storage in UTC format
 *
 * @return {Object}
 */
function getValues() {
  if (endTime.value) {
    const UTCInstance = utcMomentInstanceFromDateAndDropdownTime(
      endDate.value,
      endTime.value
    )

    return {
      date: UTCInstance.format('YYYY-MM-DD'),
      time: UTCInstance.format('HH:mm'),
    }
  }

  return {
    date: endDate.value,
    time: '',
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

useGlobalEventListener('update-end-time', value => (endTime.value = value))

useGlobalEventListener(
  'due-time-changed',
  event => (dueTime.value = event.newVal)
)

useGlobalEventListener('due-date-changed', event => {
  dueDate.value = event.newVal

  if (event.newVal != endDate.value && !event.isInitialValue) {
    endDate.value = appMoment(event.newVal).format('YYYY-MM-DD')
  } else if (event.isInitialValue && event.dueTime) {
    // Below, we will check if the due date is the same date like
    // our current end date, it can happen e.q. on dates 23:30:00
    // to go to the next day when adding 1 hour, in this case
    // the due date in local time will be 01:00:00 and the endDate will be 12:00:00
    // To test, modify the ActivityDueDateField line:
    // const UTCInstance = appMoment().add(1, 'hour').startOf('hour')
    // to
    // const UTCInstance = appMoment('23:00:00').add(1, 'hour').startOf('hour')
    const utcDueDate = utcMomentInstanceFromDateAndDropdownTime(
      dueDate.value,
      dueTime.value
    )

    const utcEndDate = utcMomentInstanceFromDateAndDropdownTime(
      endDate.value,
      endTime.value || dueTime.value
    )

    if (!utcEndDate.isSame(utcDueDate, 'day')) {
      utcEndDate.add(utcDueDate.diff(utcEndDate, 'day'), 'day')
      endDate.value = dateFromAppTimezone(
        utcEndDate.format('YYYY-MM-DD'),
        'YYYY-MM-DD'
      )
    }
  }
})
</script>
<style>
input[name='end_date-end-time'] {
  width: 116px !important;
}
</style>
