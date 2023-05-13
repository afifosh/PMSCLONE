<template>
  <div>
    <FormFolder
      v-for="(folder, index) in folders"
      :key="index + folder.name"
      :folder="folder"
      :index="index"
      :folders="folders"
    />
  </div>
</template>
<script setup>
import FormFolder from './EmailAccountFormFolder.vue'
defineProps({ folders: Array })
</script>
