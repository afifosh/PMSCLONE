<template>
  <IOverlay :show="!visible">
    <MailPlaceholders
      v-if="placeholders && componentReady"
      :placeholders="placeholders"
      v-model:visible="placeholdersVisible"
      @inserted="$emit('placeholder-inserted')"
    />
    <TinyMCE
      v-if="visible"
      v-model="internalContent"
      :disabled="disabled"
      :init="editorConfig"
    />
  </IOverlay>
</template>
<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue'
import TinyMCE from '@tinymce/tinymce-vue'
import MailPlaceholders from './MailEditorPlaceholders.vue'
import debounce from 'lodash/debounce'
import { randomString, getLocale, isDarkMode } from '@/utils'
import localeMaps from '../../components/Core/Editor/localeMaps'
import { useI18n } from 'vue-i18n'
const locale = getLocale()
const emit = defineEmits(['update:modelValue', 'init', 'placeholder-inserted'])
const props = defineProps({
  modelValue: {},
  placeholders: Object,
  placeholdersDisabled: Boolean,
  placeholder: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  withDrop: { default: false, type: Boolean },
})

const { t } = useI18n()

const visible = ref(false)
const imagesDraftId = randomString()
const placeholdersVisible = ref(false)
const componentReady = ref(false)
const internalContent = ref(null)

let timeoutClear = null
let editor = null

const defaultConfig = ref({
  menubar: false,
  body_class: props.placeholdersDisabled ? 'placeholders-disabled' : '',
  height: '200px',
  max_height: 1000,
  visual: false,
  statusbar: false,
  contextmenu: false,
  branding: false,
  // images_upload_handler: handleImageUpload,
  language: localeMaps.hasOwnProperty(locale) ? localeMaps[locale] : locale,
  automatic_uploads: true,
  images_reuse_filename: true,
  paste_data_images: props.withDrop,
  relative_urls: false,
  remove_script_host: false,
  placeholder: props.placeholder,
  browser_spellcheck: true,
  content_style: `
        ._placeholder {
          box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
          border: 0.0625rem solid #cad1d7;
          min-width: 120px;
          display: inline-block;
          height: 29px;
          border-radius: 3px;
          line-height: 25px;
          padding-right: 0.7rem;
          padding-left: 0.7rem;
        }

        ._placeholder:focus {
          border: 0.0625rem solid #93b3cf;
          outline:0;
        }

        ._placeholder[data-autofilled] {
          background-color: #f2f9ff;
        }

        .placeholders-disabled ._placeholder {
          pointer-events: none !important;
          cursor:not-allowed !important;
          background-color: #f4f5f7 !important;
          opacity:0.8;
        }
        `,

  block_formats: `${tinymce.util.I18n.translate(
    'Paragraph'
  )}=p; ${tinymce.util.I18n.translate(
    'Heading 1'
  )}=h1; ${tinymce.util.I18n.translate(
    'Heading 2'
  )}=h2; ${tinymce.util.I18n.translate(
    'Heading 3'
  )}=h3;  ${tinymce.util.I18n.translate('Heading 4')}=h4`,

  setup: instance => {
    // Not visible on mobile as the group toolbar buttons are supporting only on floating type toolbar
    instance.ui.registry.addGroupToolbarButton('alignment', {
      icon: 'align-left',
      tooltip: tinymce.util.I18n.translate('Alignment'),
      items: 'alignleft aligncenter alignright | alignjustify',
    })

    if (props.placeholders) {
      instance.ui.registry.addButton('fields', {
        text: t('core::fields.fields'),
        onAction: _ => (placeholdersVisible.value = !placeholdersVisible.value),
      })
    }

    instance.on('init', e => {
      editor = e.target
      componentReady.value = true
      emit('init')
    })

    instance.on(
      'input',
      debounce(function (e) {
        // When first time user tries to add data on the field
        // check if the placeholders are disabled, if yes, clear the value
        // and add the attribute disabled, as we are not able to add the disabled attribute before this action
        // if the disabled attribute is not added, the user will be able still to type regardless if
        // the cursor and pointer events are not allowed with CSS because of how tinymce works probably
        if (
          e.target.classList.contains('_placeholder') &&
          placeholdersDisabled.value
        ) {
          e.target.disabled = true
          e.target.value = ''
        }

        e.currentTarget &&
          e.currentTarget
            .querySelectorAll('._placeholder')
            .forEach(input => input.setAttribute('value', input.value))
      }),
      1000
    )
  },
  plugins: [
    'advlist',
    'lists',
    'autolink',
    'link',
    'image',
    'media',
    'table',
    'autoresize',
  ],
  toolbar: `fields | blocks |
                              bold italic underline strikethrough |
                              forecolor backcolor |
                              link table |
                              alignment |
                              bullist numlist |
                              blockquote removeformat undo redo`,
})

watch(internalContent, newVal => {
  if (newVal != props.modelValue) {
    emit('update:modelValue', newVal)
  }
})

watch(
  () => props.modelValue,
  newVal => {
    if (newVal != internalContent.value) {
      internalContent.value = newVal
    }
  },
  { immediate: true }
)

const editorConfig = computed(() => {
  let config = defaultConfig.value

  if (isDarkMode()) {
    config.skin = 'oxide-dark'
    config.content_css = 'dark'
  }

  return config
})

function handleImageUpload(blobInfo, progress) {
  const file = blobInfo.blob()

  // file type is only image.
  if (!/^image\//.test(file.type)) {
    failure(
      t('validation.image', {
        attribute: file.name,
      }),
      {
        remove: true,
      }
    )
    return
  }

  return new Promise((resolve, reject) => {
    const fd = new FormData()
    fd.append('file', file)

    Innoclapps.request()
      .post(`/media/pending/${imagesDraftId}`, fd)
      .then(({ data }) => resolve(data.preview_url))
      .catch(error => {
        // Nginx 413 Request Entity Too Large
        let message =
          error.message && error.message.includes('413')
            ? t('core::app.file_too_large')
            : error.response.data.message
        reject({ message: message, remove: true })
      })
  })
}

// Usage: for form reset
function setContent(content) {
  internalContent.value = content
}

function clearContent() {
  setContent('')
}

function focus() {
  editor.focus()
}

onMounted(() => {
  // https://github.com/tinymce/tinymce-vue/issues/230
  timeoutClear = setTimeout(() => (visible.value = true), 250)
})

onBeforeUnmount(() => {
  timeoutClear && clearTimeout(timeoutClear)
})

defineExpose({ focus, setContent, clearContent })
</script>
