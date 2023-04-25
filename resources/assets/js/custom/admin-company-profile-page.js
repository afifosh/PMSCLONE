/**
 *  Form Wizard
 */

'use strict';

$(function () {
  const select2 = $('.select2');

  // select2
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>');
      $this.select2({
        dropdownParent: $this.parent()
      });
    });
  }
});
$('[data-switch-toggle]').on('click', function () {
  var target = $(this).data('switch-toggle');
  $(this).is(':checked') ? $(target).removeClass('d-none') : $(target).addClass('d-none');
});
$('[data-switch-toggle-in]').on('click', function () {
  var target = $(this).data('switch-toggle-in');
  !$(this).is(':checked') ? $(target).removeClass('d-none') : $(target).addClass('d-none');
});

$('[data-switch-toggle-in-all]').on('click', function () {
  var target = $(this).data('switch-toggle-in-all');
  var hidden = true;
  var unset = $(this).data('nset');
  $('[data-switch-toggle-in-all]').each(function () {
    if (!$(this).is(':checked')) {
      hidden = false;
    }
  });
  hidden ? $(unset).val(1) : $(unset).val('');
  hidden ? $(target).addClass('d-none') : $(target).removeClass('d-none');
});

$('.btn-next').on('click', function () {
  $(this).closest('form').find('input[name="submit_type"]').val('submit');
  $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
});

$('[data-radio-toggle-in]').on('change', function () {
  var target = $(this).data('radio-toggle-in');
  $(this).val() != 'true' ? $(target).removeClass('d-none') : $(target).addClass('d-none');
});

window.setApprovalStatus = function (params) {
  params = JSON.parse(params);
  $(params.target).val(params.val);
  console.log(params.target, params.val);
};
