<template>
  <ILayout>
    <ResourceTrashedTable :resource-name="$route.params.resourceName" />
  </ILayout>
</template>
<script setup>
import ResourceTrashedTable from '~/Core/resources/js/components/Table/TrashedTable.vue'
</script>
