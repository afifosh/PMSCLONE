<template>
  <MediaCard
    :resource-name="resourceName"
    :resource-id="contact.id"
    :media="contact.media"
    :authorize-delete="contact.authorizations.update"
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
  contact: { required: true, type: Object },
})

const resourceName = Innoclapps.config('resources.contacts.name')

const { removeResourceRecordMedia, addResourceRecordMedia } = useRecordStore()
</script>
