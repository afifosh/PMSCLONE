<template>
  <DealCard
    :deals="deals"
    :empty-text="$t('contacts::company.no_deals_associated')"
  >
    <template #actions="{ deal }">
      <IButton
        v-show="authorizeDissociate"
        size="sm"
        variant="white"
        v-i-tooltip.left="$t('deals::deal.dissociate')"
        @click="dissociateDeal(deal.id)"
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
        :text="$t('deals::deal.add')"
      />
    </template>
  </DealCard>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import DealCard from '~/Deals/resources/js/components/Cards/DealCard.vue'

const emit = defineEmits(['dissociated', 'create-requested'])

const props = defineProps({
  deals: { required: true, type: Array },
  companyId: { required: true, type: Number },
  authorizeDissociate: { required: true, type: Boolean },
  showCreateButton: { required: true, type: Boolean },
})

const { t } = useI18n()

const { record: deal, removeResourceRecordHasManyRelationship } =
  useRecordStore()

async function dissociateDeal(id) {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(
    'associations/companies/' + props.companyId,
    {
      data: {
        deals: [id],
      },
    }
  )

  emit('dissociated', id)

  // When preview is shown in contact single resource view
  // We need to actually remove the relation
  if (deal.value && deal.value.id == id) {
    removeResourceRecordHasManyRelationship(props.companyId, 'companies')
  }

  Innoclapps.success(t('core::resource.dissociated'))
}
</script>
