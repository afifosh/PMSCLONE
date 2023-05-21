<template>
  <ResourceTable
    v-if="initialize"
    @loaded="$emit('loaded', $event)"
    resource-name="contacts"
    :table-id="tableId"
    :empty-state="{
      to: { name: 'create-contact' },
      title: $t('contacts::contact.empty_state.title'),
      buttonText: $t('contacts::contact.create'),
      description: $t('contacts::contact.empty_state.description'),
      secondButtonText: $t('core::import.from_file', { file_type: 'CSV' }),
      secondButtonIcon: 'DocumentAdd',
      secondButtonTo: {
        name: 'import-resource',
        params: { resourceName: 'contacts' },
      },
    }"
  >
    <template #after-search="{ collection }">
      <div class="ml-auto flex items-center text-sm">
        <span
          class="text-neutral-800 dark:text-neutral-300"
          v-t="{
            path: 'contacts::contact.count.all',
            args: { count: collection.state.meta.total },
          }"
        />
      </div>
    </template>
    <template #display_name="{ row, formatted }">
      <div class="flex w-full justify-between">
        <router-link
          class="link grow"
          :to="{ name: 'view-contact', params: { id: row.id } }"
        >
          {{ formatted }}
        </router-link>
        <div class="ml-2">
          <IMinimalDropdown>
            <IDropdownItem
              @click="
                activityBeingCreatedRow = {
                  ...row,
                  ...{ name: row.display_name },
                }
              "
              :text="$t('activities::activity.create')"
              icon="Clock"
            />
            <IDropdownItem
              @click="preview(row.id)"
              icon="Bars3CenterLeft"
              :text="$t('core::app.preview')"
            />

            <IDropdownItem
              v-if="row.authorizations.delete"
              @click="destroy(row.id)"
              icon="Trash"
              :text="$t('core::app.delete')"
            />
          </IMinimalDropdown>
        </div>
      </div>
    </template>
  </ResourceTable>

  <CreateActivityModal
    :visible="activityBeingCreatedRow !== null"
    :contacts="[activityBeingCreatedRow]"
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
    resourceName: 'contacts',
    resourceId: id,
  })
}

async function destroy(id) {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(`/contacts/${id}`)

  emit('deleted', id)

  reloadTable(props.tableId)

  Innoclapps.success(t('core::resource.deleted'))
}
</script>
