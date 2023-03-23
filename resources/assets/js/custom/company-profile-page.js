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
$('.save-draft').on('click', function () {
  $(this).closest('form').find('input[name="submit_type"]').val('draft');
  $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
});
$('.submit-and-next').on('click', function () {
  $(this).closest('form').find('input[name="submit_type"]').val('submit');
  $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
});
(function () {
  let accountUserImage = document.getElementById('uploadedAvatar');
  const fileInput = document.querySelector('.account-file-input'),
    resetFileInput = document.querySelector('.account-image-reset');

  if (accountUserImage) {
    const resetImage = accountUserImage.src;
    fileInput.onchange = () => {
      if (fileInput.files[0]) {
        accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
      }
    };
    resetFileInput.onclick = () => {
      fileInput.value = '';
      accountUserImage.src = resetImage;
    };
  }
  // Numbered Wizard
  // --------------------------------------------------------------------
  const wizardElm = $('.wizard-numbered')[0],
    companyProfileStepper = new Stepper(wizardElm, {
      linear: false
    });
  window.triggerNext = function () {
    companyProfileStepper.next();
  };
  window.triggerStep = function (step) {
    companyProfileStepper.to(0);
    companyProfileStepper.to(step);
  };
  $(document).on('click', '.btn-prev', function () {
    companyProfileStepper.previous();
  });
  $(document).on('click', '.btn-next', function () {
    companyProfileStepper.next();
  });
  wizardElm.addEventListener('show.bs-stepper', function (event) {
    const stepElm = $(wizardElm).find('.step-index-' + event.detail.indexStep)[0],
      target = $($(stepElm).data('target')),
      url = $(stepElm).data('href');
    if (url) {
      getData(url).then(function (resp) {
        $(target).html(resp.data.view_data);
        $(target)
          .find('.select2')
          .each(function () {
            if (!$(this).data('select2')) {
              var $this = $(this);
              $this.wrap('<div class="position-relative"></div>');
              $this.select2({
                dropdownParent: $this.parent()
              });
            }
          });
      });
    }
  });

  function getData(url) {
    return $.ajax({
      url: url,
      type: 'get',
      success: function (data) {
        return data;
      }
    });
  }
})();
