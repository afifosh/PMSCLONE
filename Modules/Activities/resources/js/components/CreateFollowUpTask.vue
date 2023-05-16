<template>
  <div class="flex items-center">
    <IFormCheckbox
      v-model:checked="form.with_task"
      @change="handleCheckboxChange"
      :label="$t('activities::activity.create_follow_up_task')"
    />

    <div class="ml-2 flex" v-if="form.with_task">
      <div v-show="!isCustomDateSelected">
        <IDropdown>
          <template #toggle="{ toggle }">
            <a
              href="#"
              @click="toggle"
              class="link inline-flex items-center text-sm"
            >
              {{ dropdownLabel }}
              <Icon icon="ChevronDown" class="ml-1 h-4 w-4" />
            </a>
          </template>
          <IDropdownItem
            v-for="date in dates"
            :key="date.value"
            @click="onDropdownSelected(date.value)"
            :text="date.label"
          />
        </IDropdown>
      </div>
      <DatePicker
        v-if="isCustomDateSelected"
        v-model="form.task_date"
        :required="true"
      >
        <template v-slot="{ inputValue, inputEvents }">
          <input
            :value="inputValue"
            v-on="inputEvents"
            class="cursor-pointer bg-transparent text-sm font-medium text-neutral-700 focus:outline-none dark:text-neutral-100"
          />
        </template>
      </DatePicker>
    </div>
  </div>
</template>
<script setup>
import { ref, unref, computed, nextTick } from 'vue'
import find from 'lodash/find'
import { useI18n } from 'vue-i18n'
import { useDates } from '~/Core/resources/js/composables/useDates'

const props = defineProps({
  form: { type: Object, required: true },
})

const { t } = useI18n()
const { appMoment, dateFromAppTimezone } = useDates()

const selectedDropdownDate = ref('')

/**
 * Today's date object
 */
const dateToday = computed(() => ({
  label: t('core::dates.today'),
  value: appMoment().format('YYYY-MM-DD'),
}))

/**
 * Tomorrow date object
 */
const dateTomorrow = computed(() => ({
  label: t('core::dates.tomorrow'),
  value: dateDropdownValue(1),
  default: true,
}))

/**
 * Date in 2 days object
 */
const dateIn2Days = computed(() => ({
  label: t('core::dates.in_2_days') + ' (' + dateDropdownLabel(2) + ')',
  value: dateDropdownValue(2),
}))

/**
 * Date in  days object
 */
const dateIn3Days = computed(() => ({
  label: t('core::dates.in_3_days') + ' (' + dateDropdownLabel(3) + ')',
  value: dateDropdownValue(3),
}))

/**
 * Date in 4 days object
 */
const dateIn4Days = computed(() => ({
  label: t('core::dates.in_4_days') + ' (' + dateDropdownLabel(4) + ')',
  value: dateDropdownValue(4),
}))

/**
 * Date in 5 days object
 */
const dateIn5Days = computed(() => ({
  label: t('core::dates.in_5_days') + ' (' + dateDropdownLabel(5) + ')',
  value: dateDropdownValue(5),
}))

/**
 * Date in 2 weeks object
 */
const dateIn2Weeks = computed(() => ({
  label:
    t('core::dates.in_2_weeks') +
    ' (' +
    dateDropdownLabel(2, 'weeks', 'MMMM Do') +
    ')',
  value: dateDropdownValue(2, 'weeks'),
}))

/**
 * Date in 1 month object
 */
const dateIn1Month = computed(() => ({
  label:
    t('core::dates.in_1_month') +
    ' (' +
    dateDropdownLabel(1, 'months', 'MMMM Do') +
    ')',
  value: dateDropdownValue(1, 'months'),
}))

/**
 * Whether the "custom" dropdown option is selected
 */
const isCustomDateSelected = computed(
  () => selectedDropdownDate.value === 'custom'
)

/**
 * Dates for dropdown
 */
const dates = computed(() => [
  unref(dateToday),
  unref(dateTomorrow),
  unref(dateIn2Days),
  unref(dateIn3Days),
  unref(dateIn4Days),
  unref(dateIn5Days),
  unref(dateIn2Weeks),
  unref(dateIn1Month),
  {
    label: t('core::dates.custom'),
    value: 'custom',
  },
])

/**
 * Label for the dropdown text based on selected date
 */
const dropdownLabel = computed(() => {
  let selected = find(dates.value, ['value', selectedDropdownDate.value])

  if (selected) {
    return selected.label
  }
})

/**
 * The default value
 *
 * @return {String}
 */
const defaultValue = computed(() => find(dates.value, ['default', true]).value)

/**
 * On date option selected from the dropdown
 * @param  {String} value
 * @return {Void}
 */
function onDropdownSelected(value) {
  selectedDropdownDate.value = value
  if (value !== 'custom') {
    props.form.task_date = value
  }
}

/**
 * Dropdown label date to show the actual day/date
 * @param  {Number} number
 * @param  {String} period
 * @param  {String} format
 * @return {String}
 */
function dateDropdownValue(number, period = 'days', format = 'YYYY-MM-DD') {
  return appMoment().add(number, period).format(format)
}

/**
 * Dropdown label date to show the actual day/date
 * @param  {Number} number
 * @param  {String} period
 * @param  {String} format
 * @return {String}
 */
function dateDropdownLabel(number, period = 'days', format = 'dddd') {
  return dateFromAppTimezone(appMoment().add(number, period), format)
}

/**
 * Handle the checkbox "Create follow up task" change event
 *
 * @param  {Boolean} value
 *
 * @return {Void}
 */
function handleCheckboxChange(value) {
  if (value && !props.form.task_date) {
    nextTick(() => {
      props.form.task_date = defaultValue.value
      selectedDropdownDate.value = defaultValue.value
    })
  } else if (!value) {
    props.form.task_date = null
  }
}
</script>
