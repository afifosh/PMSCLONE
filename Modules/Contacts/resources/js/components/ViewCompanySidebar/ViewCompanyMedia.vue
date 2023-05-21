<template>
  <MediaCard
    :resource-name="resourceName"
    :resource-id="company.id"
    :media="company.media"
    :authorize-delete="company.authorizations.update"
    @deleted="removeResourceRecordMedia"
    @uploaded="addResourceRecordMedia"
  />
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import MediaCard from '~/Core/resources/js/components/Media/ResourceRecordMediaCard.vue'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'

const props = defineProps({
  company: { required: true, type: Object },
})

const resourceName = Innoclapps.config('resources.companies.name')

const { removeResourceRecordMedia, addResourceRecordMedia } = useRecordStore()
</script>
