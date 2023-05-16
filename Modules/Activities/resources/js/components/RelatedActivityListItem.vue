<template>
  <ICard
    v-bind="$attrs"
    v-show="!activityBeingEdited"
    condensed
    :class="'activity-' + activityId"
    footer-class="inline-flex flex-col w-full"
    no-body
  >
    <template #header>
      <div class="flex">
        <div class="mr-2 mt-px flex shrink-0 self-start">
          <StateChange
            class="ml-px md:mt-px"
            :activity-id="activityId"
            :is-completed="isCompleted"
            :disabled="!authorizations.changeState"
            @state-changed="handleActivityStateChanged"
          />
        </div>
        <div
          class="flex grow flex-col space-y-1 md:flex-row md:space-x-3 md:space-y-0"
        >
          <div class="flex grow flex-col items-start">
            <h3
              class="truncate whitespace-normal text-base/6 font-medium text-neutral-700 dark:text-white"
              :class="{ 'line-through': isCompleted }"
              v-text="title"
            />

            <AssociationsPopover
              class="-mt-1"
              @change="syncAssociations"
              :resource-name="viaResource"
              :associateable="record"
              :disabled="syncingAssociations"
              :associated="associations"
            />
          </div>
          <TextBackground
            :color="type.swatch_color"
            class="inline-flex shrink-0 items-center self-start rounded-md py-0.5 dark:!text-white sm:rounded-full"
          >
            <FormDropdownSelect
              v-if="authorizations.update"
              :items="typesForDropdown"
              :modelValue="typeId"
              label-key="name"
              value-key="id"
              @change="updateActivity({ activity_type_id: $event.id })"
            >
              <template v-slot="{ toggle }">
                <button
                  type="button"
                  @click="toggle"
                  class="flex w-full items-center justify-between rounded-md px-2.5 text-sm leading-5 focus:outline-none"
                >
                  <div class="flex items-center">
                    <Icon :icon="type.icon" class="mr-1 h-4 w-4" />
                    {{ type.name }}

                    <Icon icon="ChevronDown" class="ml-1 h-4 w-4" />
                  </div>
                </button>
              </template>
            </FormDropdownSelect>
            <span v-else class="flex items-center px-1 text-sm">
              <Icon :icon="type.icon" class="mr-1 h-4 w-4" />
              {{ type.name }}
            </span>
          </TextBackground>
        </div>
        <div class="ml-2 mt-px inline-flex self-start md:ml-5">
          <IMinimalDropdown class="mt-1 md:mt-0.5">
            <IDropdownItem
              v-if="authorizations.update"
              @click="toggleEdit"
              :text="$t('core::app.edit')"
            />
            <IDropdownItem
              @click="downloadICS"
              :text="$t('activities::activity.download_ics')"
            />
            <IDropdownItem
              v-if="authorizations.delete"
              @click="destroy(activityId)"
              :text="$t('core::app.delete')"
            />
          </IMinimalDropdown>
        </div>
      </div>
    </template>

    <IAlert
      v-if="isDue"
      variant="warning"
      icon="Clock"
      :rounded="false"
      :class="[
        '-mt-px border-warning-100',
        Boolean(note) ? 'border-t' : 'border-y',
      ]"
      wrapper-class="-ml-px sm:ml-1.5"
    >
      {{
        $t('activities::activity.activity_was_due', {
          date: dueDate.time
            ? localizedDateTime(dueDate.date + ' ' + dueDate.time)
            : localizedDate(dueDate.date),
        })
      }}
    </IAlert>

    <div @dblclick="toggleEdit">
      <div v-if="note" class="-mt-px border border-warning-100 bg-warning-50">
        <TextCollapse
          :text="note"
          :length="100"
          class="wysiwyg-text px-4 py-2.5 text-warning-700 sm:px-6"
        >
          <template #action="{ collapsed, toggle }">
            <div
              v-show="collapsed"
              @click="toggle"
              class="absolute bottom-1 h-1/2 w-full cursor-pointer bg-gradient-to-t from-warning-50 to-transparent dark:from-warning-100"
            />

            <a
              href="#"
              v-show="!collapsed"
              class="my-2.5 inline-block px-4 text-sm font-medium text-warning-800 hover:text-warning-900 sm:px-6"
              @click.prevent="toggle"
              v-t="'core::app.show_less'"
            />
          </template>
        </TextCollapse>
      </div>

      <ICardBody condensed>
        <div class="space-y-4 sm:space-y-6">
          <div v-if="description" class="mb-8">
            <p
              class="mb-2 inline-flex text-sm font-medium text-neutral-800 dark:text-white"
            >
              <Icon icon="Bars3BottomLeft" class="mr-3 h-5 w-5 text-current" />
              <span v-t="'activities::activity.description'"></span>
            </p>
            <TextCollapse
              :text="description"
              :length="200"
              class="wysiwyg-text ml-8 dark:text-neutral-300 sm:mb-0"
            />
          </div>
          <div
            class="flex flex-col flex-wrap space-x-0 space-y-2 align-baseline lg:flex-row lg:space-x-4 lg:space-y-0"
          >
            <div
              v-if="user"
              v-i-tooltip.top="$t('activities::activity.owner')"
              class="self-start sm:self-auto"
            >
              <FormDropdownSelect
                v-if="authorizations.update"
                :items="usersForDropdown"
                :modelValue="userId"
                value-key="id"
                label-key="name"
                @change="updateActivity({ user_id: $event.id })"
              >
                <template #label="{ label }">
                  <IAvatar size="xs" class="mr-3" :src="user.avatar_url" />
                  <span
                    class="text-neutral-800 hover:text-neutral-500 dark:text-neutral-200 dark:hover:text-neutral-400"
                    v-text="label"
                  />
                </template>
              </FormDropdownSelect>
              <p
                v-else
                class="flex items-center text-sm font-medium text-neutral-800 dark:text-neutral-200"
              >
                <IAvatar size="xs" class="mr-3" :src="user.avatar_url" />
                {{ user.name }}
              </p>
            </div>

            <ActivityDateDisplay
              class="font-medium"
              :due-date="dueDate"
              :end-date="endDate"
              :is-due="isDue"
            />
          </div>
        </div>
        <p
          v-if="reminderMinutesBefore && !isReminded"
          class="mt-2 flex items-center text-sm text-neutral-800 dark:text-neutral-200 sm:mt-3"
        >
          <Icon
            icon="Bell"
            class="mr-3 h-5 w-5 text-neutral-800 dark:text-white"
          />

          <span>
            {{ reminderText }}
          </span>
        </p>
      </ICardBody>
    </div>
    <div
      class="border-y border-neutral-100 px-4 py-2.5 dark:border-neutral-800 sm:px-6"
    >
      <MediaCard
        :card="false"
        :show="attachmentsAreVisible"
        :wrapper-class="[
          'ml-8',
          {
            'py-4': attachmentsCount === 0,
            'mb-4': attachmentsCount > 0,
          },
        ]"
        class="mt-1"
        resource-name="activities"
        :resource-id="activityId"
        :media="media"
        :authorize-delete="authorizations.update"
        @deleted="handleActivityMediaDeleted"
        @uploaded="handleActivityMediaUploaded"
      >
        <template #heading>
          <p
            class="inline-flex items-center text-sm font-medium text-neutral-800 dark:text-white"
          >
            <Icon icon="PaperClip" class="mr-3 h-5 w-5 text-current" />
            <a
              href="#"
              @click.prevent="attachmentsAreVisible = !attachmentsAreVisible"
              class="inline-flex items-center focus:outline-none"
            >
              <span>
                {{ $t('core::app.attachments') }} ({{ attachmentsCount }})
              </span>
              <Icon
                :icon="attachmentsAreVisible ? 'ChevronDown' : 'ChevronRight'"
                class="ml-3 h-4 w-4"
              />
            </a>
          </p>
        </template>
      </MediaCard>
    </div>
    <div
      v-show="commentsCount"
      class="border-b border-neutral-100 px-4 py-2.5 dark:border-neutral-800 sm:px-6"
    >
      <CommentsCollapse
        :via-resource="viaResource"
        :commentable-id="activityId"
        commentable-type="activities"
        :count="commentsCount"
        @update:count="
          updateResourceRecordHasManyRelationship(
            {
              id: activityId,
              comments_count: $event,
            },
            'activities'
          )
        "
        list-wrapper-class="ml-8"
        class="mt-1"
      />
    </div>

    <template #footer>
      <AddComment
        class="self-end"
        @created="commentsAreVisible = true"
        :via-resource="viaResource"
        :commentable-id="activityId"
        commentable-type="activities"
      />
    </template>
  </ICard>

  <EditActivity
    v-if="activityBeingEdited"
    @cancelled="activityBeingEdited = false"
    @updated="activityBeingEdited = false"
    :via-resource="viaResource"
    :activity-id="activityId"
  />
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import { ref, computed } from 'vue'
import EditActivity from './RelatedActivityEdit.vue'
import ActivityDateDisplay from './ActivityDateDisplay.vue'
import CommentsCollapse from '~/Comments/resources/js/components/CommentsCollapse.vue'
import AddComment from '~/Comments/resources/js/components/AddComment.vue'
import AssociationsPopover from '~/Core/resources/js/components/AssociationsPopover.vue'
import MediaCard from '~/Core/resources/js/components/Media/ResourceRecordMediaCard.vue'
import StateChange from './ActivityStateChange.vue'
import FileDownload from 'js-file-download'
import TextBackground from '~/Core/resources/js/components/TextBackground.vue'
import { useI18n } from 'vue-i18n'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useComments } from '~/Comments/resources/js/composables/useComments'
import { useActivityTypes } from '../composables/useActivityTypes'
import { useApp } from '~/Core/resources/js/composables/useApp'

import {
  determineReminderTypeBasedOnMinutes,
  determineReminderValueBasedOnMinutes,
} from '@/utils'

const props = defineProps({
  activityId: { required: true, type: Number },
  title: { required: true, type: String },
  commentsCount: { required: true, type: Number },
  isCompleted: { required: true, type: Boolean },
  isReminded: { required: true, type: Boolean },
  isDue: { required: true, type: Boolean },
  typeId: { required: true, type: Number },
  userId: { required: true, type: Number },
  note: { required: true },
  description: { required: true },
  reminderMinutesBefore: { required: true },
  dueDate: { required: true },
  endDate: { required: true },
  attachmentsCount: { required: true, type: Number },
  media: { required: true, type: Array },
  authorizations: { required: true, type: Object },
  associations: { required: true, type: Object },
  viaResource: { required: true, type: String },
})

const { t } = useI18n()

const { localizedDateTime, localizedDate } = useDates()

const {
  record,
  updateResourceRecordHasManyRelationship,
  incrementResourceRecordCount,
  decrementResourceRecordCount,
  addResourceRecordSubRelation,
  removeResourceRecordSubRelation,
  removeResourceRecordHasManyRelationship,
} = useRecordStore()

const { users, findUserById } = useApp()

const usersForDropdown = computed(() =>
  users.value.map(user => ({ id: user.id, name: user.name }))
)

const { commentsAreVisible } = useComments(props.activityId, 'activities')

const activityBeingEdited = ref(false)
const syncingAssociations = ref(false)
const activityBeingUpdated = ref(false)
const attachmentsAreVisible = ref(false)

const { typesByName: types, findTypeById } = useActivityTypes()

const typesForDropdown = computed(() =>
  types.value.map(type => ({
    id: type.id,
    name: type.name,
    icon: type.icon,
  }))
)

const type = computed(() => findTypeById(props.typeId))

const user = computed(() => findUserById(props.userId))

const reminderText = computed(() => {
  return t('core::app.reminder_set_for', {
    value: determineReminderValueBasedOnMinutes(props.reminderMinutesBefore),
    type: determineReminderTypeBasedOnMinutes(props.reminderMinutesBefore),
  })
})

/**
 * Download ICS file for the activity
 *
 * @return {Void}
 */
function downloadICS() {
  Innoclapps.request()
    .get(`/activities/${props.activityId}/ics`, {
      responseType: 'blob',
    })
    .then(response => {
      FileDownload(
        response.data,
        response.headers['content-disposition'].split('filename=')[1]
      )
    })
}

/**
 * Update the current activity
 *
 * @param  {Object} payload
 *
 * @return {Void}
 */
function updateActivity(payload = {}) {
  activityBeingUpdated.value = true
  Innoclapps.request()
    .put(`/activities/${props.activityId}`, {
      via_resource: props.viaResource,
      via_resource_id: record.value.id,
      ...payload,
    })
    .then(({ data }) =>
      updateResourceRecordHasManyRelationship(data, 'activities')
    )
    .finally(() => (activityBeingUpdated.value = false))
}

/**
 * Delete activity from storage
 *
 * @param  {Number} id
 *
 * @return {Void}
 */
async function destroy(id) {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(`/activities/${id}`)

  removeResourceRecordHasManyRelationship(id, 'activities')
  decrementResourceRecordCount('incomplete_activities_for_user_count')

  Innoclapps.success(t('activities::activity.deleted'))
}

/**
 * Activity state changed
 *
 * @param {Object} activity
 *
 * @return {Void}
 */
function handleActivityStateChanged(activity) {
  updateResourceRecordHasManyRelationship(activity, 'activities')

  if (activity.is_completed) {
    decrementResourceRecordCount('incomplete_activities_for_user_count')
  } else {
    incrementResourceRecordCount('incomplete_activities_for_user_count')
  }
}

/**
 * Toggle edit
 *
 * @param  {Object} e
 *
 * @return {Void}
 */
function toggleEdit(e) {
  // The double click to edit should not work while in edit mode
  if (e.type == 'dblclick' && activityBeingEdited.value) return
  // For double click event
  if (!props.authorizations.update) return

  activityBeingEdited.value = !activityBeingEdited.value
}

/**
 * Handle activity media uploaded
 *
 * @param  {Object} media
 *
 * @return {Void}
 */
function handleActivityMediaUploaded(media) {
  addResourceRecordSubRelation('activities', props.activityId, 'media', media)
}

/**
 * Handle activity media deleted
 *
 * @param  {Object} media
 *
 * @return {Void}
 */
function handleActivityMediaDeleted(media) {
  removeResourceRecordSubRelation(
    'activities',
    props.activityId,
    'media',
    media.id
  )
}

/**
 * Sync the activity associations
 *
 * @param  {Object} data
 *
 * @return {Void}
 */
function syncAssociations(data) {
  syncingAssociations.value = true

  Innoclapps.request()
    .post(`associations/activities/${props.activityId}`, data)
    .then(({ data }) =>
      updateResourceRecordHasManyRelationship(data, 'activities')
    )
    .finally(() => (syncingAssociations.value = false))
}
</script>
