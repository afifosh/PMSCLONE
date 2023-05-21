<template>
  <CompanyDealCard
    :company-id="company.id"
    :deals="company.deals"
    :authorize-dissociate="company.authorizations.update"
    :show-create-button="company.authorizations.update"
    @create-requested="
      $router.push({
        name: 'createDealViaCompany',
        params: { id: company.id },
      })
    "
    @dissociated="$emit('refetch')"
  />
</template>
<script setup>
import CompanyDealCard from '../Cards/CompanyDealCard.vue'

defineEmits(['refetch'])

defineProps({
  company: { required: true, type: Object },
})
</script>
