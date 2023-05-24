<template>
  <TemplateForm :form="form">
    <template #bottom>
      <div
        class="space-x-2 divide-y divide-neutral-200 border-t border-neutral-200 pt-3 text-right dark:divide-neutral-700 dark:border-neutral-700"
      >
        <IButton
          variant="white"
          @click="$emit('cancel-requested')"
          :text="$t('core::app.cancel')"
        />
        <IButton
          type="submit"
          variant="primary"
          @click="update"
          :text="$t('core::app.save')"
        />
      </div>
    </template>
  </TemplateForm>
</template>
<script setup>
import TemplateForm from './MailTemplateForm.vue'
import pick from 'lodash/pick'
import { useI18n } from 'vue-i18n'
import { useForm } from '../../composables/Core/useForm'
import { useMailTemplates } from '../../composables/useMailTemplates'

const emit = defineEmits(['updated', 'cancel-requested'])

const props = defineProps({
  templateId: { required: true, type: Number },
})

const { t } = useI18n()
const { setTemplate, findTemplateById } = useMailTemplates()

const { form } = useForm()

function update() {
  form.put(`/mails/templates/${props.templateId}`).then(template => {
    setTemplate(template.id, template)

    emit('updated', template)

    Innoclapps.success(t('mailclient::mail.templates.updated'))
  })
}

function prepareComponent() {
  form.set(
    pick(findTemplateById(props.templateId), [
      'subject',
      'body',
      'name',
      'is_shared',
    ])
  )
}

prepareComponent()
</script>
