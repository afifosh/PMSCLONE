/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */
// Parents selector
Element.prototype.parents = function (selector) {
  var elements = []
  var elem = this
  var ishaveselector = selector !== undefined

  while ((elem = elem.parentElement) !== null) {
    if (elem.nodeType !== Node.ELEMENT_NODE) {
      continue
    }

    if (!ishaveselector || elem.matches(selector)) {
      elements.push(elem)
    }
  }

  return elements
}

function getElParentDialog(el) {
  return el.parents('.dialog')[0] || null
}

Element.prototype.parentDialogIdHash = function () {
  let dialogEl = getElParentDialog(this)

  if (!dialogEl) {
    return ''
  }

  // headless ui adds ID to the .dialog element, in this case, we will use this ID to mount the popover
  return '#' + dialogEl.getAttribute('id')
}

Element.prototype.inDialog = function () {
  return getElParentDialog(this) ? true : false
}
