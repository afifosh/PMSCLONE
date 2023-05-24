<template>
  <Lightbox v-model="activeLightboxImageIndex" :urls="imagesUrlsForLightbox">
    <template #toolbar>
      <a
        v-for="type in ['preview_url', 'download_url']"
        :key="type"
        :href="activeLightboxMedia[type]"
        :target="type === 'preview_url' ? '_blank' : undefined"
        tabindex="-1"
        rel="noopener noreferrer"
        class="toolbar-btn"
      >
        <Icon
          :icon="type === 'preview_url' ? 'Eye' : 'Download'"
          class="h-5 w-5"
        />
      </a>
    </template>
  </Lightbox>
  <dd class="text-sm text-neutral-900 dark:text-white">
    <ul
      role="list"
      class="divide-y divide-neutral-100 rounded-md border border-neutral-200 bg-white dark:divide-neutral-800 dark:border-neutral-800 dark:bg-neutral-900"
    >
      <li
        class="flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6"
        v-for="media in attachments"
        :key="media.id"
      >
        <div class="flex w-0 flex-1 items-center">
          <Icon
            icon="PaperClip"
            class="h-5 w-5 flex-shrink-0 text-neutral-400"
          />
          <div class="ml-4 flex min-w-0 flex-1 gap-2">
            <span class="truncate font-medium">
              <a
                v-if="media.aggregate_type !== 'image'"
                :href="media.view_url"
                target="_blank"
                rel="noopener noreferrer"
                tabindex="0"
                v-text="media.file_name"
              />
              <a
                v-else
                :href="media.view_url"
                @click.prevent="
                  activeLightboxImageIndex = findIndexForLightbox(
                    media.preview_url
                  )
                "
                rel="noopener noreferrer"
                tabindex="0"
                v-text="media.file_name"
              />
            </span>
            <span
              class="flex-shrink-0 text-neutral-400"
              v-text="formatBytes(media.size)"
            />
          </div>
        </div>
        <div class="ml-4 flex-shrink-0">
          <a
            :href="media.download_url"
            class="link font-medium"
            v-t="'core::app.download'"
          />
        </div>
      </li>
    </ul>
  </dd>
</template>
<script setup>
import { ref, computed } from 'vue'
import { formatBytes } from '@/utils'
import Lightbox from '../../components/Core/Lightbox/Lightbox.vue'

const props = defineProps({
  attachments: { required: true, type: Array },
})

const activeLightboxImageIndex = ref(null)

const mediaImages = computed(() =>
  props.attachments.filter(media => media.aggregate_type === 'image')
)

const imagesUrlsForLightbox = computed(() =>
  mediaImages.value.map(media => media.preview_url)
)

const activeLightboxMedia = computed(() => {
  return mediaImages.value[activeLightboxImageIndex.value]
})

function findIndexForLightbox(previewUrl) {
  return props.attachments.findIndex(media => media.preview_url === previewUrl)
}
</script>
