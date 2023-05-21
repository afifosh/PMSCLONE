<template>
  <div>
    <p
      class="inline-flex items-center text-sm font-medium text-neutral-800 dark:text-white"
      v-show="hasComments"
      v-bind="$attrs"
    >
      <span class="mr-3 h-5 w-5">
        <Icon
          icon="ChatAlt"
          v-show="!requestInProgress"
          class="h-5 w-5 text-current"
        />
        <ISpinner v-if="requestInProgress" class="mt-px h-4 w-4 text-current" />
      </span>

      <a
        href="#"
        @click="toggleCommentsVisibility"
        class="inline-flex items-center focus:outline-none"
      >
        <span
          v-t="{
            path: 'comments::comment.total',
            args: { total: countComputed },
          }"
        />
        <Icon
          :icon="commentsAreVisible ? 'ChevronDown' : 'ChevronRight'"
          class="ml-3 h-4 w-4"
        />
      </a>
    </p>

    <div
      v-show="commentsAreVisible && commentsAreLoaded"
      :class="['mt-3', listWrapperClass]"
    >
      <Comments
        v-if="commentsAreLoaded"
        :comments="commentsComputed"
        :commentable-type="commentableType"
        :commentable-id="commentableId"
        :via-resource="viaResource"
        :auto-focus-if-required="true"
        @deleted="handleCommentDeletedEvent"
      />
    </div>
  </div>
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import { computed, watch, nextTick, onBeforeUnmount } from 'vue'
import { useComments } from '../composables//useComments'
import { whenever } from '@vueuse/core'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import findIndex from 'lodash/findIndex'
import Comments from './CommentList.vue'

const emit = defineEmits(['update:count'])

const props = defineProps({
  count: Number,
  listWrapperClass: [Array, Object, String],
  commentableType: { required: true, type: String },
  commentableId: { required: true, type: Number },
  viaResource: { type: String },
})

const {
  requestInProgress,
  commentsAreVisible,
  commentsAreLoaded,
  toggleCommentsVisibility,
  getAllComments,
} = useComments(props.commentableId, props.commentableType)

const { setRecord, record, updateResourceRecordHasManyRelationship } =
  useRecordStore()

const commentsComputed = computed(() => {
  if (props.viaResource) {
    const relRecords = record.value[props.commentableType]
    return (
      relRecords[findIndex(relRecords, ['id', props.commentableId])]
        ?.comments || []
    )
  }

  return record.value.comments || []
})

const countComputed = computed(() => {
  if (!commentsAreLoaded.value) {
    return props.count || 0
  }

  return commentsComputed.value.length
})

const hasComments = computed(() => countComputed.value > 0)

/**
 * Emit the update count event when new comments are added/deleted
 * For in case any parent component interested to update it's data
 */
watch(countComputed, newVal => emit('update:count', newVal))

whenever(commentsAreVisible, () => {
  // When the comments visibility is toggled on after a comment is added
  // We don't need to make a request to load them all as we already know
  // that there were zero comments and new one was created
  if (
    props.count === 0 &&
    commentsComputed.value.length === 1 &&
    commentsComputed.value[0].was_recently_created === true
  ) {
    commentsAreLoaded.value = true

    return
  }

  if (!commentsAreLoaded.value) {
    // TODO, fires twice because of timeline and direct embed
    loadComments()
  }
})

async function loadComments() {
  let comments = await getAllComments(
    props.viaResource,
    props.viaResource ? record.value.id : null
  )

  commentsAreLoaded.value = true

  if (props.viaResource) {
    updateResourceRecordHasManyRelationship(
      {
        id: props.commentableId,
        comments: comments,
      },
      props.commentableType
    )

    return
  }

  setRecord({ comments })
}

async function handleCommentDeletedEvent(comment) {
  await nextTick()

  if (!hasComments.value) {
    commentsAreVisible.value = false
  }
}

onBeforeUnmount(() => {
  commentsAreVisible.value = false
  commentsAreLoaded.value = false
})
</script>
