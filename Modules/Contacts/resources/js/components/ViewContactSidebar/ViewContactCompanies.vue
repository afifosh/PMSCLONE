<template>
  <ContactCompanyCard
    :contact-id="contact.id"
    :companies="contact.companies"
    :authorize-dissociate="contact.authorizations.update"
    :show-create-button="contact.authorizations.update"
    @create-requested="
      $router.push({
        name: 'createCompanyViaContact',
        params: { id: contact.id },
      })
    "
    @dissociated="$emit('refetch')"
  />
</template>
<script setup>
import ContactCompanyCard from '../Cards/ContactCompanyCard.vue'

defineProps({
  contact: { required: true, type: Object },
})

defineEmits(['refetch'])
</script>
