<template>
  <p
    :class="[
      'flex items-center text-sm',
      {
        'text-danger-500 dark:text-danger-400': isDue,
        'text-neutral-800 dark:text-neutral-200': !isDue,
      },
    ]"
  >
    <Icon
      v-if="withIcon"
      icon="Calendar"
      :class="[
        'mr-2 h-5 w-5',
        {
          'text-neutral-800 dark:text-white': !isDue,
          'text-danger-400': isDue,
        },
      ]"
    />
    {{
      dueDate.time
        ? localizedDateTime(dueDate.date + ' ' + dueDate.time)
        : localizedDate(dueDate.date)
    }}
    <span v-if="isEndDateBiggerThenDueDate" class="ml-1">
      -
      {{ localizedDateTime(endDate.date + ' ' + endDate.time) }}
    </span>
    <span v-else-if="isEndDateEqualToDueDate" class="ml-1">
      -
      {{
        localizedDateTime(
          endDate.date + ' ' + endDate.time,
          timeFormatForMoment
        )
      }}
    </span>
  </p>
</template>
<script setup>
import { computed } from 'vue'
import { useDates } from '~/Core/resources/js/composables/useDates'

const { localizedDate, localizedDateTime, currentTimeFormat } = useDates()

const props = defineProps({
  dueDate: { required: true },
  endDate: { required: true },
  isDue: { required: true, type: Boolean },
  withIcon: { type: Boolean, default: true },
})

const isEndDateBiggerThenDueDate = computed(() => {
  return (
    props.endDate.date &&
    props.endDate.time &&
    localizedDateTime(
      props.endDate.date + ' ' + props.endDate.time,
      'YYYY-MM-DD'
    ) >
      localizedDateTime(
        props.dueDate.date + ' ' + props.dueDate.time,
        'YYYY-MM-DD'
      )
  )
})

const isEndDateEqualToDueDate = computed(() => {
  return (
    props.endDate.date &&
    props.endDate.time &&
    localizedDateTime(
      props.endDate.date + ' ' + props.endDate.time,
      'YYYY-MM-DD'
    ) ==
      localizedDateTime(
        props.dueDate.date + ' ' + props.dueDate.time,
        'YYYY-MM-DD'
      )
  )
})

const timeFormatForMoment = computed(() =>
  moment().PHPconvertFormat(currentTimeFormat.value)
)
</script>
