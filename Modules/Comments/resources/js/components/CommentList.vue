<template>
  <div ref="commentsRef">
    <p
      v-if="!hasComments"
      class="text-center text-sm text-neutral-500 dark:text-neutral-400"
      v-t="'comments::comment.no_comments'"
    />

    <Comment
      v-for="comment in comments"
      :key="comment.id"
      class="mb-3"
      :commentable-type="commentableType"
      :commentable-id="commentableId"
      :via-resource="viaResource"
      :comment-id="comment.id"
      :highlighted="comment.id === highlightedCommentId"
      :body="comment.body"
      :created-by="comment.created_by"
      :created-at="comment.created_at"
      :authorizations="comment.authorizations"
      @deleted="$emit('deleted', $event)"
    />
  </div>
</template>
<script setup>
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import Comment from './CommentListItem.vue'

const emit = defineEmits(['deleted'])

const props = defineProps({
  autoFocusIfRequired: Boolean,
  comments: Array,
  commentableType: { required: true, type: String },
  commentableId: { required: true, type: Number },
  viaResource: { type: String },
})

const route = useRoute()

const commentsRef = ref(null)
const highlightedCommentId = ref(null)
let bgTimeoutClear = null

const hasComments = computed(() => props.comments.length > 0)

function focusIfRequired() {
  if (!route.query.comment_id || !props.autoFocusIfRequired) {
    return
  }

  nextTick(() => {
    const $comment = commentsRef.value.querySelector(
      '#comment-' + route.query.comment_id
    )

    if ($comment) {
      $comment.scrollIntoView({
        behavior: 'auto',
        block: 'center',
        inline: 'nearest',
      })

      highlightedCommentId.value = Number(route.query.comment_id)

      bgTimeoutClear = setTimeout(
        () => (highlightedCommentId.value = null),
        10000
      )
    }
  })
}

onMounted(focusIfRequired)

onBeforeUnmount(() => {
  bgTimeoutClear && clearTimeout(bgTimeoutClear)
})
</script>
