<template>
  <ILayout>
    <template #actions>
      <NavbarSeparator class="hidden lg:block" />
      <div class="inline-flex items-center">
        <div class="mr-3 lg:mr-6">
          <IMinimalDropdown type="horizontal">
            <IDropdownItem
              icon="DocumentAdd"
              :to="{
                name: 'import-resource',
                params: { resourceName: 'contacts' },
              }"
              :text="$t('core::import.import')"
            />
            <IDropdownItem
              v-if="$gate.userCan('export contacts')"
              icon="DocumentDownload"
              @click="$iModal.show('export-modal')"
              :text="$t('core::app.export.export')"
            />
            <IDropdownItem
              icon="Trash"
              :to="{
                name: 'trashed-resource-records',
                params: { resourceName: 'contacts' },
              }"
              :text="$t('core::app.soft_deletes.trashed')"
            />
            <IDropdownItem
              icon="Cog"
              @click="customizeTable(tableId)"
              :text="$t('core::table.list_settings')"
            />
          </IMinimalDropdown>
        </div>
        <IButton
          :to="{ name: 'create-contact' }"
          icon="Plus"
          size="sm"
          :text="$t('contacts::contact.create')"
        />
      </div>
    </template>

    <CardList v-if="showCards" resource-name="contacts" />

    <ContactTable
      :table-id="tableId"
      :initialize="initialize"
      @loaded="tableEmpty = $event.empty"
      @deleted="refreshIndex"
    />

    <ContactExport
      url-path="/contacts/export"
      resource-name="contacts"
      :filters-view="tableId"
      :title="$t('contacts::contact.export')"
    />

    <!-- Create -->
    <router-view
      name="create"
      :redirect-to-view="true"
      @created="
        ({ isRegularAction }) => (!isRegularAction ? refreshIndex() : undefined)
      "
      @modal-hidden="$router.back"
    />
  </ILayout>
</template>
<script setup>
import { ref, computed } from 'vue'
import CardList from '~/Core/resources/js/components/Cards/CardList.vue'
import ContactTable from '../../components/ContactTable.vue'
import ContactExport from '~/Core/resources/js/components/Export'
import { onBeforeRouteUpdate, useRoute } from 'vue-router'
import { useTable } from '~/Core/resources/js/components/Table/useTable'

const route = useRoute()

const initialize = ref(route.meta.initialize)
const tableEmpty = ref(true)
const tableId = 'contacts'

const { reloadTable, customizeTable } = useTable()

const showCards = computed(() => initialize.value && !tableEmpty.value)

function refreshIndex() {
  Innoclapps.$emit('refresh-cards')
  reloadTable(tableId)
}

/**
 * Before the cached route is updated
 * For all cases set that intialize index to be true
 * This helps when intially "initialize" was false
 * But now when the user actually sees the index, it should be updated to true
 */
onBeforeRouteUpdate((to, from, next) => {
  initialize.value = true

  next()
})
</script>
