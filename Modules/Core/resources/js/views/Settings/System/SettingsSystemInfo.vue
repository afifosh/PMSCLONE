<template>
  <ICard :header="$t('core::app.system_info')" no-body>
    <template #actions>
      <IButton
        @click="download"
        size="sm"
        icon="DocumentDownload"
        variant="white"
      />
    </template>
    <ITable>
      <tbody>
        <tr v-for="(value, variableName) in info" :key="variableName">
          <td>
            <span class="font-medium" v-text="variableName"></span>
          </td>
          <td>
            <span
              class="text-neutral-500 dark:text-neutral-300"
              v-text="value"
            ></span>
          </td>
        </tr>
      </tbody>
    </ITable>
  </ICard>
</template>
<script setup>
import { ref } from 'vue'
import FileDownload from 'js-file-download'
const info = ref({})

function retrieve() {
  Innoclapps.request()
    .get('/system/info')
    .then(({ data }) => (info.value = data))
}

function download() {
  Innoclapps.request()
    .post(
      '/system/info',
      {},
      {
        responseType: 'blob',
      }
    )
    .then(response => {
      FileDownload(response.data, 'system-info.xlsx')
    })
}

retrieve()
</script>
