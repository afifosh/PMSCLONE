<template>
  <CompanyCard
    v-if="hasParents"
    :title="$t('contacts::company.child', { count: totalParents })"
    :companies="company.parents"
  />
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import { computed } from 'vue'
import CompanyCard from '../Cards/CompanyCard.vue'

const props = defineProps({
  company: { required: true, type: Object },
})

const totalParents = computed(() => props.company.parents.length)

const hasParents = computed(() => totalParents.value > 0)
</script>
