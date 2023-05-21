<template>
  <ResourceTable
    v-if="initialize"
    @loaded="$emit('loaded', $event)"
    resource-name="companies"
    :table-id="tableId"
    :empty-state="{
      to: { name: 'create-company' },
      title: $t('contacts::company.empty_state.title'),
      buttonText: $t('contacts::company.create'),
      description: $t('contacts::company.empty_state.description'),
      secondButtonText: $t('core::import.from_file', { file_type: 'CSV' }),
      secondButtonIcon: 'DocumentAdd',
      secondButtonTo: {
        name: 'import-resource',
        params: { resourceName: 'companies' },
      },
    }"
  >
    <template #after-search="{ collection }">
      <div class="ml-auto flex items-center text-sm">
        <span
          class="text-neutral-800 dark:text-neutral-300"
          v-t="{
            path: 'contacts::company.count.all',
            args: { count: collection.state.meta.total },
          }"
        />
      </div>
    </template>
    <template #domain="{ formatted }">
      <a
        v-show="formatted"
        :href="'http://' + formatted"
        target="blank"
        class="link flex items-center"
      >
        {{ formatted }} <Icon icon="ExternalLink" class="ml-1 h-4 w-4" />
      </a>
    </template>
    <template #name="{ row, formatted }">
      <div class="flex w-full justify-between">
        <router-link
          class="link grow"
          :to="{ name: 'view-company', params: { id: row.id } }"
        >
          {{ formatted }}
        </router-link>
        <div class="ml-2">
          <IMinimalDropdown>
            <IDropdownItem
              @click="activityBeingCreatedRow = row"
              icon="Clock"
              :text="$t('activities::activity.create')"
            />
            <IDropdownItem
              @click="preview(row.id)"
              icon="Bars3CenterLeft"
              :text="$t('core::app.preview')"
            />

            <IDropdownItem
              v-if="row.authorizations.delete"
              @click="destroy(row.id)"
              :text="$t('core::app.delete')"
              icon="Trash"
            />
          </IMinimalDropdown>
        </div>
      </div>
    </template>
  </ResourceTable>

  <CreateActivityModal
    :visible="activityBeingCreatedRow !== null"
    :companies="[activityBeingCreatedRow]"
    :with-extended-submit-buttons="true"
    :go-to-list="false"
    @created="
      ({ isRegularAction }) => (
        isRegularAction ? (activityBeingCreatedRow = null) : '',
        reloadTable(tableId)
      )
    "
    @modal-hidden="activityBeingCreatedRow = null"
  />
  <PreviewModal />
</template>
<script setup>
import { ref } from 'vue'
import ResourceTable from '~/Core/resources/js/components/Table'
import { useStore } from 'vuex'
import { useTable } from '~/Core/resources/js/components/Table/useTable'
import { useI18n } from 'vue-i18n'

const emit = defineEmits(['deleted', 'loaded'])

const props = defineProps({
  tableId: { required: true, type: String },
  initialize: { default: true, type: Boolean },
})

const { t } = useI18n()
const store = useStore()

const { reloadTable } = useTable()

const activityBeingCreatedRow = ref(null)

function preview(id) {
  store.commit('recordPreview/SET_PREVIEW_RESOURCE', {
    resourceName: 'companies',
    resourceId: id,
  })
}

async function destroy(id) {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(`/companies/${id}`)

  emit('deleted', id)

  reloadTable(props.tableId)

  Innoclapps.success(t('core::resource.deleted'))
}
</script>
