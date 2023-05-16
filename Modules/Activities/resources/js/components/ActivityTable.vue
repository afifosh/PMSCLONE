<template>
  <div class="mb-2 block lg:hidden">
    <ActivityTableTypePicker v-model="selectedType" />
  </div>
  <ResourceTable
    v-if="initialize"
    resource-name="activities"
    :row-class="rowClass"
    :data-request-query-string="dataRequestQueryString"
    :table-id="tableId"
    :empty-state="{
      to: { name: 'create-activity' },
      title: $t('activities::activity.empty_state.title'),
      buttonText: $t('activities::activity.create'),
      description: $t('activities::activity.empty_state.description'),
      secondButtonText: $t('core::import.from_file', { file_type: 'CSV' }),
      secondButtonIcon: 'DocumentAdd',
      secondButtonTo: {
        name: 'import-resource',
        params: { resourceName: 'activities' },
      },
    }"
    v-bind="$attrs"
  >
    <template #after-search="{ collection }">
      <div class="hidden lg:ml-6 lg:block">
        <ActivityTableTypePicker v-model="selectedType" />
      </div>
      <div class="ml-auto flex items-center text-sm">
        <span
          class="text-neutral-800 dark:text-neutral-300"
          v-t="{
            path: 'activities::activity.count',
            args: { count: collection.state.meta.total },
          }"
        />
      </div>
    </template>
    <template #title="{ row, formatted }">
      <div class="flex w-full justify-between">
        <router-link
          class="link grow"
          :to="{ name: 'edit-activity', params: { id: row.id } }"
        >
          {{ formatted }}
        </router-link>
        <div class="ml-2">
          <IMinimalDropdown>
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

  <!-- Edit/View -->
  <router-view name="edit" />
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import { ref, computed } from 'vue'
import ResourceTable from '~/Core/resources/js/components/Table'
import ActivityTableTypePicker from './ActivityTableTypePicker.vue'
import { useTable } from '~/Core/resources/js/components/Table/useTable'
import { useI18n } from 'vue-i18n'

const emit = defineEmits(['deleted'])

const props = defineProps({
  tableId: { required: true, type: String },
  initialize: { default: true, type: Boolean },
})

const { t } = useI18n()

const { reloadTable } = useTable()

const selectedType = ref(undefined)

const dataRequestQueryString = computed(() => ({
  activity_type_id: selectedType.value,
}))

function rowClass(row) {
  return {
    'has-warning': true,
    'warning-confirmed': row.is_due,
  }
}

async function destroy(id) {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(`/activities/${id}`)

  emit('deleted', id)
  reloadTable(props.tableId)

  Innoclapps.success(t('core::resource.deleted'))
}
</script>
