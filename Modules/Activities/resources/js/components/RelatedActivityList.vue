<template>
  <div class="space-y-4">
    <div v-for="activity in activities" :key="activity.id">
      <ActivityView
        :activity-id="activity.id"
        :title="activity.title"
        :comments-count="activity.comments_count"
        :is-completed="activity.is_completed"
        :is-reminded="activity.is_reminded"
        :is-due="activity.is_due"
        :type-id="activity.activity_type_id"
        :user-id="activity.user_id"
        :note="activity.note"
        :description="activity.description"
        :reminder-minutes-before="activity.reminder_minutes_before"
        :due-date="activity.due_date"
        :end-date="activity.end_date"
        :attachments-count="activity.media.length"
        :media="activity.media"
        :associations="activity.associations"
        :authorizations="activity.authorizations"
        :via-resource="viaResource"
      />
    </div>
  </div>
</template>
<script setup>
import ActivityView from './RelatedActivityListItem.vue'

const props = defineProps({
  activities: Array,
  viaResource: { type: String, required: true },
})
</script>
