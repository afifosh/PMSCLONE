<template>
  <CompanyContactCard
    :company-id="company.id"
    :contacts="company.contacts"
    :authorize-dissociate="company.authorizations.update"
    :show-create-button="company.authorizations.update"
    @create-requested="
      $router.push({
        name: 'createContactViaCompany',
        params: { id: company.id },
      })
    "
    @dissociated="$emit('refetch')"
  />
</template>
<script setup>
import CompanyContactCard from '../Cards/CompanyContactCard.vue'

defineEmits(['refetch'])

defineProps({
  company: { required: true, type: Object },
})
</script>
