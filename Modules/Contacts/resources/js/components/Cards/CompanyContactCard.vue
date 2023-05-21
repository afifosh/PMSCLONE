<template>
  <ContactCard
    :contacts="contacts"
    :empty-text="$t('contacts::company.no_contacts_associated')"
  >
    <template #actions="{ contact }">
      <IButton
        v-show="authorizeDissociate"
        size="sm"
        variant="white"
        v-i-tooltip.left="$t('contacts::contact.dissociate')"
        @click="dissociateContact(contact.id)"
        icon="X"
      />
    </template>
    <template #tail>
      <IButton
        v-if="showCreateButton"
        variant="white"
        class="mt-6"
        block
        @click="$emit('create-requested')"
        :text="$t('contacts::contact.add')"
      />
    </template>
  </ContactCard>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import ContactCard from './ContactCard.vue'

const emit = defineEmits(['dissociated', 'create-requested'])

const props = defineProps({
  contacts: { required: true, type: Array },
  companyId: { required: true, type: Number },
  authorizeDissociate: { required: true, type: Boolean },
  showCreateButton: { required: true, type: Boolean },
})

const { t } = useI18n()
const { record: contact, removeResourceRecordHasManyRelationship } =
  useRecordStore()

async function dissociateContact(id) {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(
    'associations/companies/' + props.companyId,
    {
      data: {
        contacts: [id],
      },
    }
  )

  emit('dissociated', id)
  // When preview is shown in contact single resource view
  // We need to actually remove the relation
  if (contact.value && contact.value.id == id) {
    removeResourceRecordHasManyRelationship(props.companyId, 'companies')
  }

  Innoclapps.success(t('core::resource.dissociated'))
}
</script>
