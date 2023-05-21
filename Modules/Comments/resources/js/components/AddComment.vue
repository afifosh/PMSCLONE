<template>
  <a
    v-show="!commengIsBeingCreated"
    href="#"
    v-bind="$attrs"
    @click.prevent="commengIsBeingCreated = true"
    class="link inline-flex items-center text-sm"
  >
    <Icon icon="Plus" class="mr-1 h-4 w-4" /> {{ $t('comments::comment.add') }}
  </a>
  <CreateComment
    v-if="commengIsBeingCreated"
    :commentable-type="commentableType"
    :commentable-id="commentableId"
    :via-resource="viaResource"
    @created="handleCommentCreated"
    @cancelled="commengIsBeingCreated = false"
  />
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import { onUnmounted } from 'vue'
import { useComments } from '../composables//useComments'
import CreateComment from './CreateComment.vue'

const emit = defineEmits(['created'])

const props = defineProps({
  commentableType: { required: true, type: String },
  commentableId: { required: true, type: Number },
  viaResource: { type: String },
})

const { commengIsBeingCreated } = useComments(
  props.commentableId,
  props.commentableType
)

function handleCommentCreated(comment) {
  emit('created', comment)
}

onUnmounted(() => {
  commengIsBeingCreated.value = false
})
</script>
