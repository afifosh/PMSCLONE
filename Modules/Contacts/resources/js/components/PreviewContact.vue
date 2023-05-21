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

    <ContactCompanyCard
      class="mb-3 sm:mb-5"
      :contact-id="record.id"
      :companies="record.companies"
      :authorize-dissociate="record.authorizations.update"
      :show-create-button="record.authorizations.update"
      @dissociated="
        removeResourceRecordHasManyRelationship($event, 'companies')
      "
      @create-requested="companyBeingCreated = true"
    />

    <ContactDealCard
      class="mb-3 sm:mb-5"
      :contact-id="record.id"
      :deals="record.deals"
      :authorize-dissociate="record.authorizations.update"
      :show-create-button="record.authorizations.update"
      @dissociated="removeResourceRecordHasManyRelationship($event, 'deals')"
      @create-requested="dealBeingCreated = true"
    />

    <MediaCard
      class="mb-3 sm:mb-5"
      resource-name="contacts"
      :resource-id="record.id"
      :media="record.media"
      :authorize-delete="record.authorizations.update"
      :is-floating="true"
      @uploaded="addResourceRecordMedia"
      @deleted="removeResourceRecordMedia"
    />
  </div>

  <CreateCompanyModal
    v-model:visible="companyBeingCreated"
    :overlay="false"
    :contacts="[record]"
    @created="refreshPreview(), (companyBeingCreated = false)"
    @restored="refreshPreview(), (companyBeingCreated = false)"
  />

  <CreateDealModal
    v-model:visible="dealBeingCreated"
    :overlay="false"
    :contacts="[record]"
    @created="refreshPreview(), (dealBeingCreated = false)"
    @restored="refreshPreview(), (dealBeingCreated = false)"
  />
</template>
<script setup>
import { ref, inject } from 'vue'
import MediaCard from '~/Core/resources/js/components/Media/ResourceRecordMediaCard.vue'
import ContactCompanyCard from './Cards/ContactCompanyCard.vue'
import ContactDealCard from './Cards/ContactDealCard.vue'
import FieldsCollapseButton from '~/Core/resources/js/components/Fields/ButtonCollapse.vue'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'

const props = defineProps({
  record: Object,
  form: Object,
  fields: Object,
  hasCollapsibleFields: Boolean,
})

const fieldsCollapsed = ref(true)

const companyBeingCreated = ref(false)
const dealBeingCreated = ref(false)

const refreshPreview = inject('refreshPreviewRecord')

const {
  addResourceRecordMedia,
  removeResourceRecordMedia,
  removeResourceRecordHasManyRelationship,
} = useRecordStore('recordPreview', 'record')
</script>
