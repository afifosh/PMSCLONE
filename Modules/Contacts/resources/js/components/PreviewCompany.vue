<template>
  <div>
    <FieldsGenerator
      :fields="fields"
      :form-id="form.formId"
      :collapsed="fieldsCollapsed"
      view="update"
      :is-floating="true"
    >
      <template #after="{ fields }">
        <FieldsCollapseButton
          v-if="hasCollapsibleFields"
          v-model:collapsed="fieldsCollapsed"
          class="mb-3"
        />
      </template>
    </FieldsGenerator>

    <CompanyContactCard
      class="mb-3 sm:mb-5"
      :company-id="record.id"
      :contacts="record.contacts"
      :authorize-dissociate="record.authorizations.update"
      :show-create-button="record.authorizations.update"
      @dissociated="removeResourceRecordHasManyRelationship($event, 'contacts')"
      @create-requested="contactBeingCreated = true"
    />

    <CompanyDealCard
      class="mb-3 sm:mb-5"
      :company-id="record.id"
      :deals="record.deals"
      :authorize-dissociate="record.authorizations.update"
      :show-create-button="record.authorizations.update"
      @dissociated="removeResourceRecordHasManyRelationship($event, 'deals')"
      @create-requested="dealBeingCreated = true"
    />

    <MediaCard
      class="mb-3 sm:mb-5"
      resource-name="companies"
      :resource-id="record.id"
      :media="record.media"
      :authorize-delete="record.authorizations.update"
      :is-floating="true"
      @uploaded="addResourceRecordMedia"
      @deleted="removeResourceRecordMedia"
    />
  </div>

  <CreateContactModal
    v-model:visible="contactBeingCreated"
    :overlay="false"
    :companies="[record]"
    @created="refreshPreview(), (contactBeingCreated = false)"
    @restored="refreshPreview(), (contactBeingCreated = false)"
  />

  <CreateDealModal
    v-model:visible="dealBeingCreated"
    :overlay="false"
    :companies="[record]"
    @created="refreshPreview(), (dealBeingCreated = false)"
    @restored="refreshPreview(), (dealBeingCreated = false)"
  />
</template>
<script setup>
import { ref, inject } from 'vue'
import MediaCard from '~/Core/resources/js/components/Media/ResourceRecordMediaCard.vue'
import CompanyDealCard from './Cards/CompanyDealCard.vue'
import CompanyContactCard from './Cards/CompanyContactCard.vue'
import FieldsCollapseButton from '~/Core/resources/js/components/Fields/ButtonCollapse.vue'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'

const props = defineProps({
  record: Object,
  form: Object,
  fields: Object,
  hasCollapsibleFields: Boolean,
})

const fieldsCollapsed = ref(true)

const contactBeingCreated = ref(false)
const dealBeingCreated = ref(false)

const refreshPreview = inject('refreshPreviewRecord')

const {
  addResourceRecordMedia,
  removeResourceRecordMedia,
  removeResourceRecordHasManyRelationship,
} = useRecordStore('recordPreview', 'record')
</script>
