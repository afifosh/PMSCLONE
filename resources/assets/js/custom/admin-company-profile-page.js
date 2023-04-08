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

$('.save-draft').on('click', function () {
  $(this).closest('form').find('input[name="submit_type"]').val('draft');
  $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
});
$('.btn-next').on('click', function () {
  $(this).closest('form').find('input[name="submit_type"]').val('submit');
  $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
});

$('[data-radio-toggle-in]').on('change', function () {
  var target = $(this).data('radio-toggle-in');
  $(this).val() != 'true' ? $(target).removeClass('d-none') : $(target).addClass('d-none');
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
  const wizardNumbered = document.querySelector('.wizard-numbered'),
    // wizardNumberedBtnNextList = [].slice.call(wizardNumbered.querySelectorAll('.btn-next')),
    wizardNumberedBtnPrevList = [].slice.call(wizardNumbered.querySelectorAll('.btn-prev')),
    wizardNumberedBtnSubmit = wizardNumbered.querySelector('.btn-submit');

  if (typeof wizardNumbered !== undefined && wizardNumbered !== null) {
    const numberedStepper = new Stepper(wizardNumbered, {
      linear: false
    });
    window.triggerNext = function () {
      numberedStepper.next();
    };
    // if (wizardNumberedBtnNextList) {
    //   wizardNumberedBtnNextList.forEach(wizardNumberedBtnNext => {
    //     wizardNumberedBtnNext.addEventListener('click', event => {
    //       numberedStepper.next();
    //     });
    //   });
    // }
    if (wizardNumberedBtnPrevList) {
      wizardNumberedBtnPrevList.forEach(wizardNumberedBtnPrev => {
        wizardNumberedBtnPrev.addEventListener('click', event => {
          numberedStepper.previous();
        });
      });
    }
    if (wizardNumberedBtnSubmit) {
      wizardNumberedBtnSubmit.addEventListener('click', event => {
        alert('Submitted..!!');
      });
    }
  }

  var formRepeater = $('.form-repeater');
  if (formRepeater.length) {
    var row = 2;
    var col = 1;
    formRepeater.on('submit', function (e) {
      e.preventDefault();
    });
    formRepeater.repeater({
      show: function () {
        var fromControl = $(this).find('.form-control, .form-select');
        var formLabel = $(this).find('.form-label');

        fromControl.each(function (i) {
          var id = 'form-repeater-' + row + '-' + col;
          $(fromControl[i]).attr('id', id);
          $(formLabel[i]).attr('for', id);
          col++;
        });

        row++;

        $(this).slideDown();
        $(this).find('.select2').each(function() {
          if (!$(this).data('select2')) {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
              dropdownParent: $this.parent()
            });
          }
        });
      },
      hide: function (e) {
        confirm('Are you sure you want to delete this element?') && $(this).slideUp(e);
      },
      isFirstItemUndeletable: true,
      afterAdd: function (repeaterItem) {
        alert('t');
        // Initialize Select2 for all select elements in the newly added repeater item
        $(repeaterItem).find('.select2').each(function() {
          alert('t');
          if (!$(this).data('select2')) {
            $(this).select2();
          }
        });
      }
    });
  }
})();
