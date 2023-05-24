<template>
  <IVerticalNavigationItem :active-class="activeClass" :to="folderRoute" fixed>
    <template #title>
      <div class="flex w-full">
        <div class="grow">{{ folder.display_name }}</div>
        <IBadge
          v-if="folder.unread_count"
          size="circle"
          :text="folder.unread_count"
        />
      </div>
    </template>
    <template v-if="hasChildren">
      <InboxFolderMenuItem
        v-for="child in folder.children"
        :key="child.id"
        :folder="child"
      />
    </template>
  </IVerticalNavigationItem>
</template>
<script>
export default {
  name: 'InboxFolderMenuItem',
}
</script>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  folder: { required: true, type: Object },
})

const route = useRoute()

const folderRoute = computed(() => {
  // When the user first access the INBOX menu without any params
  // the account may be undefined till the inbox.vue redirects to the
  // messages using the default account
  // in this case, while all these actions are executed just return null
  // because it's throwing warning missing account params for name route 'inbox-messages'
  if (!route.params.account_id) {
    return null
  }

  return {
    name: 'inbox-messages',
    params: {
      account_id: route.params.account_id,
      folder_id: props.folder.id,
    },
  }
})

const hasChildren = computed(
  () => props.folder.children && props.folder.children.length > 0
)

const activeClass = computed(() => {
  return props.folder.id == route.params.folder_id &&
    props.folder.email_account_id == route.params.account_id
    ? 'active'
    : ''
})
</script>
