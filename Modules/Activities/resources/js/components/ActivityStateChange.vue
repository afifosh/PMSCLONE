<template>
  <span class="inline-block" v-i-tooltip="tooltipContent">
    <a href="#" :class="linkClasses" @click.prevent="changeState">
      <ISpinner
        v-if="requestInProgress"
        class="h-4 w-4"
        :class="{
          'text-success-500 dark:text-success-400': !isCompleted,
          'text-neutral-500 dark:text-neutral-300': isCompleted,
        }"
      />
      <span
        v-else
        class="flex h-4 w-4 items-center justify-center rounded-full border"
      >
        <Icon v-if="isCompleted" icon="Check"></Icon>
      </span>
    </a>
  </span>
</template>
<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
const emit = defineEmits(['state-changed'])

const props = defineProps({
  activityId: { required: true, type: Number },
  isCompleted: { required: true, type: Boolean },
  disabled: Boolean,
})

const { t } = useI18n()
const requestInProgress = ref(false)

const linkClasses = computed(() => {
  let classes = ['inline-block mr-0.5 focus:outline-none']

  if (props.isCompleted) {
    classes.push(
      'text-success-500 hover:text-neutral-500 dark:text-success-400 dark:hover:text-neutral-300'
    )
  } else {
    classes.push(
      'text-neutral-500 hover:text-success-600 dark:text-neutral-300 dark:hover:text-success-400'
    )
  }

  if (props.disabled || requestInProgress.value) {
    classes.push('pointer-events-none opacity-60')
  }

  return classes
})

const tooltipContent = computed(() => {
  if (props.disabled) {
    return t('users::user.not_authorized')
  }

  if (props.isCompleted) {
    return t('activities::activity.mark_as_incomplete')
  }

  return t('activities::activity.mark_as_completed')
})

/**
 * Mark the activity as complete
 */
function complete() {
  return Innoclapps.request().post(`activities/${props.activityId}/complete`)
}

/**
 * Mark the activity as incomplete
 */
function incomplete() {
  return Innoclapps.request().post(`activities/${props.activityId}/incomplete`)
}

/**
 * Change state
 */
function changeState() {
  requestInProgress.value = true
  ;(props.isCompleted ? incomplete() : complete())
    .then(({ data: activity }) => emit('state-changed', activity))
    .finally(() => (requestInProgress.value = false))
}
</script>
