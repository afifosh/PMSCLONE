<template>
  <div>
    <div v-show="!isCreatingOrEditing">
      <div class="mb-3 text-right">
        <IButton
          variant="primary"
          @click="creatingTemplate = true"
          size="sm"
          :text="$t('mailclient::mail.templates.create')"
        />
      </div>
      <ITable bordered>
        <thead>
          <tr>
            <th
              class="text-left"
              width="50%"
              v-t="'mailclient.mail.templates.name'"
            />
            <th class="text-left" v-t="'core::app.created_by'" />
            <th class="text-left" v-t="'core::app.last_modified_at'" />
          </tr>
        </thead>
        <tbody>
          <tr v-for="(template, index) in templatesByName" :key="template.id">
            <td width="50%">
              <div class="flex">
                <div class="grow">
                  <p
                    class="text-neutral-800 dark:text-neutral-100"
                    v-text="template.name"
                  />
                  <p class="text-sm text-neutral-600 dark:text-neutral-300">
                    {{ $t('mailclient::mail.templates.subject') }}:
                    {{ template.subject }}
                  </p>
                </div>
                <div class="flex items-center space-x-2">
                  <IButton
                    size="sm"
                    variant="primary"
                    :text="$t('mailclient::mail.templates.select')"
                    @click="handleTemplateSelected(index)"
                  />

                  <IMinimalDropdown
                    v-if="
                      template.authorizations.update ||
                      template.authorizations.delete
                    "
                  >
                    <IDropdownItem
                      v-if="template.authorizations.update"
                      @click="templateBeingUpdated = template.id"
                      :text="$t('core::app.edit')"
                    />
                    <IDropdownItem
                      v-if="template.authorizations.delete"
                      @click="destroy(template.id)"
                      :text="$t('core::app.delete')"
                    />
                  </IMinimalDropdown>
                </div>
              </div>
            </td>
            <td>
              {{ template.user.name }}
            </td>
            <td>
              {{ localizedDateTime(template.updated_at) }}
            </td>
          </tr>
          <tr class="bg-white" v-show="!hasTemplates">
            <td :colspan="3" class="p-5 text-center">
              <div class="text-center" v-t="'core::table.empty'"></div>
            </td>
          </tr>
        </tbody>
      </ITable>
    </div>
    <CreateTemplate
      v-if="creatingTemplate"
      @cancel-requested="creatingTemplate = false"
      @created="creatingTemplate = false"
    />
    <EditTemplate
      v-if="templateBeingUpdated"
      @updated="templateBeingUpdated = null"
      @cancel-requested="templateBeingUpdated = null"
      :template-id="templateBeingUpdated"
    />
  </div>
</template>
<script setup>
import { ref, computed } from 'vue'
import CreateTemplate from './CreateMailTemplate.vue'
import EditTemplate from './EditMailTemplate.vue'
import { useI18n } from 'vue-i18n'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useMailTemplates } from '../../composables/useMailTemplates'

const emit = defineEmits(['selected'])

const { t } = useI18n()
const { localizedDateTime } = useDates()
const { templatesByName, deleteTemplate } = useMailTemplates()

const templateBeingUpdated = ref(null)
const creatingTemplate = ref(false)

const hasTemplates = computed(() => templatesByName.value.length > 0)

const isCreatingOrEditing = computed(
  () => templateBeingUpdated.value || creatingTemplate.value
)

async function destroy(id) {
  await Innoclapps.dialog().confirm()
  await deleteTemplate(id)

  Innoclapps.success(t('mailclient::mail.templates.deleted'))
}

function handleTemplateSelected(index) {
  emit('selected', templatesByName.value[index])
}
</script>
