<template>
  <CompanyCard
    :companies="companies"
    :empty-text="$t('contacts::contact.no_companies_associated')"
  >
    <template #actions="{ company }">
      <IButton
        v-show="authorizeDissociate"
        size="sm"
        variant="white"
        v-i-tooltip.left="$t('contacts::company.dissociate')"
        @click="dissociateCompany(company.id)"
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
        :text="$t('contacts::company.add')"
      />
    </template>
  </CompanyCard>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import CompanyCard from './CompanyCard.vue'

const emit = defineEmits(['dissociated', 'create-requested'])

const props = defineProps({
  companies: { required: true, type: Array },
  contactId: { required: true, type: Number },
  authorizeDissociate: { required: true, type: Boolean },
  showCreateButton: { required: true, type: Boolean },
})

const { t } = useI18n()

const { record: company, removeResourceRecordHasManyRelationship } =
  useRecordStore()

async function dissociateCompany(id) {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(
    'associations/contacts/' + props.contactId,
    {
      data: {
        companies: [id],
      },
    }
  )

  emit('dissociated', id)

  // When preview is shown in deal single resource view
  // We need to actually remove the relation
  if (company.value && company.value.id == id) {
    removeResourceRecordHasManyRelationship(props.contactId, 'contacts')
  }

  Innoclapps.success(t('core::resource.dissociated'))
}
</script>
