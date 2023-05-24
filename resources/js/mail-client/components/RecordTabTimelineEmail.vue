<template>
  <TimelineEntry
    :resource-name="resourceName"
    :created-at="log.created_at"
    :is-pinned="log.is_pinned"
    :timelineable-id="log.id"
    :timeline-relationship="log.timeline_relation"
    :timelineable-key="log.timeline_key"
    icon="Mail"
    :heading="$t('mailclient::inbox.message')"
    heading-class="font-medium"
  >
    <template #date>
      {{ localizedDateTime(log.date) }}
    </template>
    <div class="mt-3">
      <Email :email="log" :via-resource="resourceName" />
    </div>
  </TimelineEntry>
</template>
<script setup>
import { useDates } from '../composables/useDates'
import Email from '../views/Emails/RelatedMessageListItem.vue'
import TimelineEntry from './Core/Timeline/RecordTabTimelineTemplate.vue'
import propsDefinition from './Core/Timeline/props'
defineProps(propsDefinition)

const { localizedDateTime } = useDates()
</script>
