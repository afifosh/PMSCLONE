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
$('.btn-next').on('click', function () {
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
  const wizardElm = $('.wizard-numbered')[0]
  const  wizardNumberedBtnPrevList = [].slice.call($(wizardElm).find('.btn-prev'));
  const companyProfileStepper = new Stepper(wizardElm, {
    linear: false
  });
  window.triggerNext = function () {
    companyProfileStepper.next();
  };
  window.triggerStep = function (step) {
    companyProfileStepper.to(0);
    companyProfileStepper.to(step);
  };
  if (wizardNumberedBtnPrevList) {
    wizardNumberedBtnPrevList.forEach(wizardNumberedBtnPrev => {
      wizardNumberedBtnPrev.addEventListener('click', event => {
        companyProfileStepper.previous();
      });
    });
  }
  wizardElm.addEventListener('show.bs-stepper', function (event) {
    const stepElm = $(wizardElm).find('.step-index-' + event.detail.indexStep)[0];
    const target = $($(stepElm).data('target'));
    const url = $(stepElm).data('href');
    if (url){
      getData(url).then(function (resp) {
        $(target).html(resp.data.view_data);
      });
    }
  });

  function getData(url){
    return $.ajax({
      url: url,
      type: 'get',
      success: function (data) {
        return data;
      }
    });
  }
  // Form Wizard

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
