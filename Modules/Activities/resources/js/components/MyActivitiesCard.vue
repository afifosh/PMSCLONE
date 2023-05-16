<template>
  <CardTableAsync :card="card" ref="tableRef">
    <template #actions>
      <IButtonMinimal
        variant="primary"
        icon="Plus"
        :text="$t('activities::activity.create')"
        @click="activityBeingCreated = true"
        class="hide-when-editing mt-2 sm:mt-0"
      />
      <CreateActivityModal
        :visible="activityBeingCreated"
        @created="handleActivityCreatedEvent"
        @modal-hidden="activityBeingCreated = false"
      />
    </template>
    <template #empty="slotProps">
      <div
        class="flex flex-col justify-center"
        :class="{
          'items-center py-4': !slotProps.search && !slotProps.loading,
        }"
      >
        <Icon
          icon="Check"
          class="h-9 w-9 text-success-500"
          v-show="!slotProps.search && !slotProps.loading"
        />

        <p
          class="text-neutral-500 dark:text-neutral-300"
          :class="{ 'mt-2 text-lg': !slotProps.search && !slotProps.loading }"
        >
          <span v-show="slotProps.loading" v-text="slotProps.text"></span>

          <span v-show="!slotProps.loading">
            {{
              slotProps.search ? slotProps.text : $t('core::app.all_caught_up')
            }}
          </span>
        </p>
      </div>
    </template>
    <template #title="{ row, formatted }">
      <div class="flex items-center">
        <div class="mr-1.5 mt-1">
          <StateChange
            :activity-id="row.id"
            :is-completed="row.is_completed"
            :disabled="!row.authorizations.changeState"
            @state-changed="reloadTable"
          />
        </div>
        <router-link
          class="link"
          :to="{ name: 'view-activity', params: { id: row.id } }"
        >
          {{ formatted }}
        </router-link>
      </div>
    </template>
    <template #type.name="{ row, formatted }">
      <TextBackground
        :color="row.type.swatch_color"
        class="inline-flex items-center self-start rounded-full font-normal leading-5 dark:!text-white"
      >
        <span class="flex items-center px-2.5 text-sm">
          <Icon :icon="row.type.icon" class="mr-1 h-4 w-4" />
          {{ formatted }}
        </span>
      </TextBackground>
    </template>
  </CardTableAsync>
</template>
<script setup>
import { ref } from 'vue'
import StateChange from './ActivityStateChange.vue'
import TextBackground from '~/Core/resources/js/components/TextBackground.vue'

const props = defineProps({ card: Object })

const tableRef = ref(null)
const activityBeingCreated = ref(false)

function handleActivityCreatedEvent() {
  reloadTable()
  activityBeingCreated.value = false
}

function reloadTable() {
  tableRef.value.reload()
}
</script>
<style scoped>
:deep(tr > td) {
  position: relative;
}

:deep(tr > td:first-child:after),
:deep(tr > td:first-child:before) {
  content: '';
  position: absolute;
  left: 0;
  width: 100%;
}

:deep(td.due:first-child:before),
:deep(td.due:first-child:after) {
  width: auto;
  height: 100%;
  top: 0;
  border-left: 3px solid rgba(var(--color-danger-500), 1);
}

:deep(td.not-due:first-child:before),
:deep(td.not-due:first-child:after) {
  width: auto;
  height: 100%;
  top: 0;
  border-left: 3px solid transparent;
}
</style>
