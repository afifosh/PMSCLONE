import { useApp } from '../../composables/useApp'

export function useSignature() {
  const { currentUser } = useApp()

  function addSignature(message = '') {
    return (
      message +
      (currentUser.value.mail_signature
        ? '<br /><br />----------<br />' + currentUser.value.mail_signature
        : '')
    )
  }

  return {
    addSignature,
  }
}
