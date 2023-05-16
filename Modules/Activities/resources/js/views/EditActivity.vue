<template>
  <ISlideover
    :visible="true"
    @hidden="handleModalHidden"
    @shown="handleModalShown"
    form
    @submit="update"
    :ok-disabled="recordForm.busy || $gate.denies('update', record)"
    :ok-title="$t('core::app.save')"
    :hide-footer="activeTabIndex === 1"
    static-backdrop
    id="editActivityModal"
    :initial-focus="modalCloseElement"
    :title="recordForm.title"
    :description="modalDescription"
  >
    <div class="absolute -top-2 right-5 sm:top-4">
      <Actions
        v-if="fields.isNotEmpty()"
        type="dropdown"
        :ids="actionId"
        :actions="actions"
        :resource-name="resourceName"
        @run="handleActionExecuted"
      />
    </div>

    <FieldsPlaceholder v-if="fields.isEmpty()" />

    <div v-else class="mt-6 sm:mt-0">
      <ITabGroup v-model="activeTabIndex">
        <ITabList>
          <ITab :title="$t('activities::activity.activity')" />
          <ITab
            @activated.once="loadComments"
            :title="
              $t('comments::comment.comments') +
              ' (' +
              record.comments_count +
              ')'
            "
          />
        </ITabList>
        <ITabPanels>
          <ITabPanel>
            <p
              v-if="componentReady"
              class="small mb-3 text-neutral-800 dark:text-neutral-300"
            />

            <FieldsGenerator
              :fields="fields"
              class="mb-5"
              view="update"
              :form-id="recordForm.formId"
            >
              <template #after-deals-field>
                <span class="-mt-2 block text-right">
                  <a
                    href="#"
                    @click.prevent="dealBeingCreated = true"
                    class="link text-sm"
                  >
                    + {{ $t('deals::deal.create') }}
                  </a>
                </span>
              </template>

              <template #after-companies-field>
                <span class="-mt-2 block text-right">
                  <a
                    href="#"
                    @click.prevent="companyBeingCreated = true"
                    class="link text-sm"
                  >
                    + {{ $t('contacts::company.create') }}
                  </a>
                </span>
              </template>

              <template #after-contacts-field>
                <span class="-mt-2 block text-right">
                  <a
                    href="#"
                    @click.prevent="contactBeingCreated = true"
                    class="link text-sm"
                  >
                    + {{ $t('contacts::contact.create') }}
                  </a>
                </span>
              </template>
            </FieldsGenerator>

            <MediaCard
              :resource-name="resourceName"
              :resource-id="record.id"
              :media="record.media"
              :authorize-delete="record.authorizations.update"
              @deleted="removeResourceRecordMedia"
              @uploaded="addResourceRecordMedia"
            />
          </ITabPanel>
          <ITabPanel lazy>
            <div class="my-3 text-right">
              <AddComment
                :commentable-id="record.id"
                commentable-type="activities"
                @created="incrementResourceRecordCount('comments_count')"
              />
            </div>

            <IOverlay :show="!commentsLoaded">
              <Comments
                v-if="commentsLoaded"
                :comments="record.comments || []"
                commentable-type="activities"
                :commentable-id="record.id"
                :auto-focus-if-required="true"
                @deleted="decrementResourceRecordCount('comments_count')"
              />
            </IOverlay>
          </ITabPanel>
        </ITabPanels>
      </ITabGroup>
    </div>

    <CreateDealModal
      v-model:visible="dealBeingCreated"
      :overlay="false"
      @created="
        fields.mergeValue('deals', $event.deal), (dealBeingCreated = false)
      "
    />

    <CreateContactModal
      v-model:visible="contactBeingCreated"
      :overlay="false"
      @created="
        fields.mergeValue('contacts', $event.contact),
          (contactBeingCreated = false)
      "
      @restored="
        fields.mergeValue('contacts', $event), (contactBeingCreated = false)
      "
    />

    <CreateCompanyModal
      v-model:visible="companyBeingCreated"
      :overlay="false"
      @created="
        fields.mergeValue('companies', $event.company),
          (companyBeingCreated = false)
      "
      @restored="
        fields.mergeValue('companies', $event), (companyBeingCreated = false)
      "
    />
  </ISlideover>
</template>
<script setup>
import { ref, computed, watch, onBeforeMount } from 'vue'
import { computedWithControl } from '@vueuse/shared'
import Actions from '~/Core/resources/js/components/Actions/Actions.vue'
import MediaCard from '~/Core/resources/js/components/Media/ResourceRecordMediaCard.vue'
import Comments from '~/Comments/resources/js/components/CommentList.vue'
import AddComment from '~/Comments/resources/js/components/AddComment.vue'
import { useResourceUpdate } from '~/Core/resources/js/composables/useResourceUpdate'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useRoute, useRouter } from 'vue-router'
import { useApp } from '~/Core/resources/js/composables/useApp'
import { useI18n } from 'vue-i18n'
import { useComments } from '~/Comments/resources/js/composables/useComments'

const props = defineProps({
  onHidden: Function,
  onActionExecuted: Function,
  id: Number,
})

const resourceName = Innoclapps.config('resources.activities.name')

const { t } = useI18n()
const router = useRouter()
const route = useRoute()

const {
  isReady: componentReady,
  form: recordForm,
  fields,
  update,
  init,
  boot,
} = useResourceUpdate(resourceName)

const {
  addResourceRecordMedia,
  removeResourceRecordMedia,
  incrementResourceRecordCount,
  decrementResourceRecordCount,
  setRecord,
  record,
} = useRecordStore()

const { setPageTitle } = useApp()
const { localizedDateTime } = useDates()

// TODO, should use comments loaded from useComments?
const commentsLoaded = ref(false)

const dealBeingCreated = ref(false)
const contactBeingCreated = ref(false)
const companyBeingCreated = ref(false)

// Provide initial focus element as the modal can be nested and it's not
// finding an element for some reason when the second modal is closed
// showing error "There are no focusable elements inside the <FocusTrap />"
const modalCloseElement = computedWithControl(
  () => null,
  () => document.querySelector('#modalClose-editActivityModal')
)

const activeTabIndex = ref(route.query.comment_id ? 1 : 0)

const modalDescription = computed(() => {
  if (!componentReady.value) {
    return null
  }

  return `${t('core::app.created_at')}: ${localizedDateTime(
    record.value.created_at
  )} - ${record.value.creator.name}`
})

const actionId = computed(() => record.value.id || [])
const actions = computed(() => record.value.actions || [])
const computedId = computed(() => Number(props.id || route.params.id))

const { getAllComments } = useComments(computedId.value, resourceName)

function loadComments() {
  getAllComments().then(comments => {
    setRecord({ comments })
    commentsLoaded.value = true
  })
}

function handleActionExecuted(action) {
  if (props.onActionExecuted) {
    props.onActionExecuted(action)
    return
  }

  // Reload the record data on any action executed except delete
  // If we try to reload on delete will throw 404 error
  if (!action.destroyable) {
    init(record)
  } else {
    router.push({ name: 'activity-index' })
  }
}

function handleModalShown() {
  modalCloseElement.trigger()
}

function handleModalHidden() {
  props.onHidden ? props.onHidden() : router.back()
}

watch(
  () => record.value.display_name,
  newVal => {
    setPageTitle(newVal)
  }
)

onBeforeMount(async () => {
  const { data: activity } = await Innoclapps.request().get(
    `/activities/${computedId.value}`
  )

  setRecord(activity)

  boot(activity, {
    onAfterUpdate: record => {
      setRecord(record)
      Innoclapps.success(t('core::resource.updated'))
    },
    onReady: record => {
      fields.value.update('guests', {
        activity: record,
      })
    },
  })
})
</script>
