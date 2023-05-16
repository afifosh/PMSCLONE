<template>
  <ITab
    :badge="countIncomplete"
    badge-variant="danger"
    :title="$t('activities::activity.activities')"
    icon="Calendar"
  />
</template>
<script setup>
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { computed } from 'vue'

const { record } = useRecordStore()

/**
 * Record incomplete activities count
 *
 * We will check if the actual resource record incomplete_activities is 0 but the actual
 * loaded activities have incomplete, in this case, we will return the value from the loaded activities
 *
 * This may happen e.q. if there is a workflows e.q. company created => create activity
 * But because the workflow is executed on app terminating, the resource record data
 * is already retrieved before termination and the incomplete_activities_for_user_count will be 0
 */
const countIncomplete = computed(() => {
  const incomplete = (record.value.activities || []).filter(
    activity => !activity.is_completed
  )

  let count = record.value.incomplete_activities_for_user_count

  if (count === 0 && incomplete.length > 0) {
    return incomplete.length
  }

  return count
})
</script>
