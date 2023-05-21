<template>
  <div :id="'add-comment-' + commentableType + '-' + commentableId">
    <Editor
      :placeholder="$t('comments::comment.add_placeholder')"
      v-model="form.body"
      :with-mention="true"
      @init="() => $refs.editorRef.focus()"
      @input="form.errors.clear('body')"
      ref="editorRef"
    />
    <IFormError v-text="form.getError('body')" />
    <div class="mt-2 flex justify-end space-x-2">
      <IButton
        variant="white"
        @click="$emit('cancelled')"
        size="sm"
        :text="$t('core::app.cancel')"
      />
      <IButton
        variant="secondary"
        @click="create"
        size="sm"
        :disabled="form.busy"
        :text="$t('core::app.save')"
      />
    </div>
  </div>
</template>
<script setup>
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useI18n } from 'vue-i18n'
import { useForm } from '~/Core/resources/js/composables/useForm'

const emit = defineEmits(['created', 'cancelled'])

const props = defineProps({
  commentableType: { required: true, type: String },
  commentableId: { required: true, type: Number },
  viaResource: { type: String },
})

const { t } = useI18n()

const { form } = useForm({ body: '' }, { resetOnSuccess: true })

const {
  record,
  addResourceRecordHasManyRelationship,
  addResourceRecordSubRelation,
} = useRecordStore()

function handleCommentCreated(comment) {
  props.viaResource
    ? addResourceRecordSubRelation(
        props.commentableType,
        props.commentableId,
        'comments',
        comment
      )
    : addResourceRecordHasManyRelationship(comment, 'comments')

  emit('created', comment)

  Innoclapps.success(t('comments::comment.created'))
}

function create() {
  if (props.viaResource) {
    form.withQueryString({
      via_resource: props.viaResource,
      via_resource_id: record.value.id,
    })
  }

  const url = `${props.commentableType}/${props.commentableId}/comments`

  form.post(url).then(handleCommentCreated)
}
</script>
