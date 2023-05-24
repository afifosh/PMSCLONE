import { ref, computed } from 'vue'
import orderBy from 'lodash/orderBy'
import { createGlobalState } from '@vueuse/core'
import { useLoader } from './Core/useLoader'

export const useMailTemplates = createGlobalState(() => {
  const { setLoading, isLoading: templatesAreBeingFetched } = useLoader()

  const mailTemplates = ref([])

  const templatesByName = computed(() => orderBy(mailTemplates.value, 'name'))

  // Only excuted once
  fetchMailTemplates()

  function idx(id) {
    return mailTemplates.value.findIndex(template => template.id == id)
  }

  function findTemplateById(id) {
    return mailTemplates.value[idx(id)]
  }

  function removeTemplate(id) {
    mailTemplates.value.splice(idx(id), 1)
  }

  function addTemplate(template) {
    mailTemplates.value.push(template)
  }

  function setTemplate(id, template) {
    mailTemplates.value[idx(id)] = template
  }

  async function fetchTemplate(id, options = {}) {
    const { data } = await Innoclapps.request().get(
      '/mails/templates/' + id,
      options
    )
    return data
  }

  async function deleteTemplate(id) {
    await Innoclapps.request().delete(`/mails/templates/${id}`)
    removeTemplate(id)
  }

  function fetchMailTemplates() {
    setLoading(true)

    Innoclapps.request()
      .get('/mails/templates')
      .then(({ data }) => (mailTemplates.value = data))
      .finally(() => setLoading(false))
  }

  return {
    mailTemplates,
    templatesByName,
    templatesAreBeingFetched,

    addTemplate,
    removeTemplate,
    setTemplate,
    findTemplateById,

    fetchMailTemplates,
    fetchTemplate,
    deleteTemplate,
  }
})
