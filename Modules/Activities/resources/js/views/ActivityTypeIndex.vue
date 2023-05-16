<template>
  <RelatedFieldResource
    resource-name="activity-types"
    @created="fetchActivityTypes"
    @updated="fetchActivityTypes"
    @deleted="fetchActivityTypes"
    :with-cancel="false"
  >
    <template #header>
      <ICardHeading>{{ $t('activities::activity.type.types') }}</ICardHeading>
    </template>
  </RelatedFieldResource>
</template>
<script setup>
import RelatedFieldResource from '~/Core/resources/js/components/SimpleResourceCRUD.vue'
import { useActivityTypes } from '../composables/useActivityTypes'

const { fetchActivityTypes } = useActivityTypes()
</script>
