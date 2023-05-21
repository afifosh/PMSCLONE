<template>
  <ContactDealCard
    :contact-id="contact.id"
    :deals="contact.deals"
    :authorize-dissociate="contact.authorizations.update"
    :show-create-button="contact.authorizations.update"
    @create-requested="
      $router.push({
        name: 'createDealViaContact',
        params: { id: contact.id },
      })
    "
    @dissociated="$emit('refetch')"
  />
</template>
<script setup>
import ContactDealCard from '../Cards/ContactDealCard.vue'

defineProps({
  contact: { required: true, type: Object },
})

defineEmits(['refetch'])
</script>
