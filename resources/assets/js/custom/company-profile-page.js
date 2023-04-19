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
$(document).on('click', '[data-switch-toggle]', function () {
  var target = $(this).data('switch-toggle');
  $(this).is(':checked') ? $(target).removeClass('d-none') : $(target).addClass('d-none');
});
$(document).on('click', '[data-switch-toggle-in]', function () {
  var target = $(this).data('switch-toggle-in');
  !$(this).is(':checked') ? $(target).removeClass('d-none') : $(target).addClass('d-none');
});
$('.save-draft').on('click', function () {
  $(this).closest('form').find('input[name="submit_type"]').val('draft');
  $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
});
$(document).on('click', '.submit-and-next', function () {
  $(this).closest('form').find('input[name="submit_type"]').val('submit');
  $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
});
$(document).on('change', '.account-file-input', function () {
  if (this.files && this.files[0]) {
    $('#uploadedAvatar').attr('src', window.URL.createObjectURL(this.files[0]))
  }
});
$(document).on('click', '.account-image-reset', function () {
  $('#uploadedAvatar').attr('src', $('#uploadedAvatar').data('default'));
  $('.account-file-input').val('');
});
(function () {
  // --------------------------------------------------------------------
  // Numbered Wizard
  // --------------------------------------------------------------------
  const wizardElm = $('.wizard-numbered')[0];
  console.log(wizardElm && true);
  if (wizardElm) {
    window.companyProfileStepper = new Stepper(wizardElm, {
        linear: false
      });
    window.triggerNext = function () {
      companyProfileStepper.next();
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
            if(event.detail.indexStep == 3){
              handleKycDocsStepper();
            }
        });
      }
    });
  }

  window.triggerStep = function (step) {
    if(wizardElm){
      companyProfileStepper.to(0);
      step = step == 1 ? 2 : step;
      companyProfileStepper.to(step);
      if(step == 4){
        initWizard();
      }
    }else if(step === 1){
      reload_company_details();
    }else if(step === 2){
      reload_company_contacts();
    }else if(step === 3){
      reload_company_addresses();
    }else if(step === 5){
      reload_company_bank_acc();
    }
  };

  window.reload_company_details = function () {
    const url = $('#details-card').data('href');
    getData(url).then(function (resp) {
      $('#details-card').find('.collapse').html(resp.data.view_data);
      $('#details-card').find('.select2').each(function () {
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

  window.reload_company_contacts = function () {
    const url = $('#contact-persons-card').data('href');
    getData(url).then(function (resp) {
      $('#contact-persons-card').find('.collapse').html(resp.data.view_data);
    });
  }

  window.reload_company_addresses = function () {
    const url = $('#addresses-card').data('href');
    getData(url).then(function (resp) {
      $('#addresses-card').find('.collapse').html(resp.data.view_data);
    });
  }

  window.reload_company_bank_acc = function () {
    const url = $('#accounts-card').data('href');
    getData(url).then(function (resp) {
      $('#accounts-card').find('.collapse').html(resp.data.view_data);
    });
  }

  $(document).on('click', '[data-toggle-view]', function () {
    const target = $(this).data('toggle-view');
    const url = $(this).data('href');
    getData(url).then(function (resp) {
      $(target).html(resp.data.view_data);
      $(target).find('.select2').each(function () {
        if (!$(this).data('select2')) {
          var $this = $(this);
          $this.wrap('<div class="position-relative"></div>');
          $this.select2({
            dropdownParent: $this.parent()
          });
        }
      });
    });
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

  function handleKycDocsStepper(){
    if(typeof initWizard == 'function'){
      initWizard();
    }
  }
  // Vertical Wizard Init
  // --------------------------------------------------------------------
  window.initWizard = function () {
    const wizardVertical = document.querySelector('.wizard-vertical'),
      wizardVerticalBtnNextList = [].slice.call(wizardVertical.querySelectorAll('.doc-btn-next')),
      wizardVerticalBtnPrevList = [].slice.call(wizardVertical.querySelectorAll('.doc-btn-prev')),
      wizardVerticalBtnSubmit = wizardVertical.querySelector('.btn-submit');

    if (typeof wizardVertical !== undefined && wizardVertical !== null) {
      window.verticalStepper = new Stepper(wizardVertical, {
        linear: false
      });
      if (wizardVerticalBtnNextList) {
        wizardVerticalBtnNextList.forEach(wizardVerticalBtnNext => {
          wizardVerticalBtnNext.addEventListener('click', event => {
            verticalStepper.next();
          });
        });
      }
      if (wizardVerticalBtnPrevList) {
        wizardVerticalBtnPrevList.forEach(wizardVerticalBtnPrev => {
          wizardVerticalBtnPrev.addEventListener('click', event => {
            verticalStepper.previous();
          });
        });
      }

      if (wizardVerticalBtnSubmit) {
        wizardVerticalBtnSubmit.addEventListener('click', event => {
          alert('Submitted..!!');
        });
      }
      initDropzone();

      wizardVertical.addEventListener('show.bs-stepper', function (event) {
        const stepElm = $(wizardVertical).find('.step-index-' + event.detail.indexStep)[0],
          target = $($(stepElm).data('target')),
          url = $(stepElm).data('href');
        if (url) {
          getData(url).then(function (resp) {
            $(wizardVertical).find('.step').each(function () {
              if($(this).data('target') != $(stepElm).data('target')){
                $($(this).data('target')).html('');
              }
            });
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
              initDropzone();
          });
        }
      });
    }

    window.triggerNextDoc = function (step) {
      console.log(step);
      if(step != -1){
        verticalStepper.to(step);
      }else{
        triggerNext.next();
      }

    }
  }

  initWizard();

  function initDropzone()
  {
    // previewTemplate: Updated Dropzone default previewTemplate
    // ! Don't change it unless you really know what you are doing
    const previewTemplate = `<div class="dz-preview dz-file-preview">
        <div class="dz-details">
          <div class="dz-thumbnail">
            <img data-dz-thumbnail>
            <span class="dz-nopreview">No preview</span>
            <div class="dz-success-mark"></div>
            <div class="dz-error-mark"></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="progress">
              <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
            </div>
          </div>
          <div class="dz-filename" data-dz-name></div>
          <div class="dz-size" data-dz-size></div>
        </div>
      </div>`;

    $('.dropzone').each(function(){
      var $this = this;
      const dropzone = new Dropzone($this, {
        // const dropzoneMulti = new Dropzone('#dropzone-multi', {
        previewTemplate: previewTemplate,
        parallelUploads: 4,
        maxFiles: 1,
        addRemoveLinks: true,
        chunking: true,
        method: "POST",
        maxFilesize: 100,
        chunkSize: 1900000,
        autoProcessQueue : true,
        // If true, the individual chunks of a file are being uploaded simultaneously.
        parallelChunkUploads: true,
        retryChunks: true,
        acceptedFiles: 'text/plain,application/*,image/*,video/*,audio/*',
        url: $($this).data('upload-url'), //"{{ route('admin.draft-rfps.files.store', ['draft_rfp' => $draft_rfp]) }}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(file, response) {
          $($($this).data('response')).val(response.data.file_path);
            console.log(response);
        },
        init: function(){
            /* Called once the file has been processed. It could have failed or succeded */
            this.on("complete", function(file){

            });
            /* Called after the file is uploaded and sucessful */
            this.on("sucess", function(file){

            });
            /* Called before the file is being sent */
            this.on("sending", function(file){
            });
            this.on("error", function(file, errorMessage, xhr){
              // Check if the response is a validation error
              if (xhr.status === 422) {
                // Parse the validation errors from the response
                var errors = JSON.parse(xhr.responseText).errors;

                // Loop through the validation errors and add them to the file preview
                $.each(errors, function(key, value) {
                  var error = value[0];
                  var dzError = $('<div>').addClass('dz-error-message').text(error);
                  $(file.previewElement).append(dzError);
                });
              }
            })
        }
      });
      if($($this).data('file-path')){
        var img = document.createElement("img");
        img.src = $($this).data('file-path');
        dropzone.emit("addedfile", img);
        dropzone.emit("thumbnail", img, $($this).data('file-path'));
        dropzone.emit("complete", img);
        dropzone.files.push(img);
        img.classList.add('dz-success');
        img.classList.add('dz-complete');
      }
    });
  }

  /**
   * Cards Actions
   */
  const collapseElementList = [].slice.call(document.querySelectorAll('.card-collapsible'));
  const expandElementList = [].slice.call(document.querySelectorAll('.card-expand'));
  const closeElementList = [].slice.call(document.querySelectorAll('.card-close'));

  let cardDnD = document.getElementById('sortable-4');

  // Collapsible card
  // --------------------------------------------------------------------
  if (collapseElementList) {
    collapseElementList.map(function (collapseElement) {
      collapseElement.addEventListener('click', event => {
        event.preventDefault();
        // Collapse the element
        new bootstrap.Collapse(collapseElement.closest('.card').querySelector('.collapse'));
        // Toggle collapsed class in `.card-header` element
        collapseElement.closest('.card-header').classList.toggle('collapsed');
        // Toggle class ti-chevron-down & ti-chevron-right
        Helpers._toggleClass(collapseElement.firstElementChild, 'ti-chevron-down', 'ti-chevron-right');
      });
    });
  }

  // Card Toggle fullscreen
  // --------------------------------------------------------------------
  if (expandElementList) {
    expandElementList.map(function (expandElement) {
      expandElement.addEventListener('click', event => {
        event.preventDefault();
        // Toggle class ti-arrows-maximize & ti-arrows-minimize
        Helpers._toggleClass(expandElement.firstElementChild, 'ti-arrows-maximize', 'ti-arrows-minimize');

        expandElement.closest('.card').classList.toggle('card-fullscreen');
      });
    });
  }

  // Toggle fullscreen on esc key
  document.addEventListener('keyup', event => {
    event.preventDefault();
    //Esc button
    if (event.key === 'Escape') {
      const cardFullscreen = document.querySelector('.card-fullscreen');
      // Toggle class ti-arrows-maximize & ti-arrows-minimize

      if (cardFullscreen) {
        Helpers._toggleClass(cardFullscreen.querySelector('.card-expand').firstChild, 'ti-arrows-maximize', 'ti-arrows-minimize');
        cardFullscreen.classList.toggle('card-fullscreen');
      }
    }
  });

  // Card close
  // --------------------------------------------------------------------
  if (closeElementList) {
    closeElementList.map(function (closeElement) {
      closeElement.addEventListener('click', event => {
        event.preventDefault();
        closeElement.closest('.card').classList.add('d-none');
      });
    });
  }

  // Card reload (jquery)
  // --------------------------------------------------------------------

  const cardReload = $('.card-reload');
  if (cardReload.length) {
    cardReload.on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      var url = $this.parents('.card').data('href');
      $this.closest('.card').block({
        message:
          '<div class="sk-fold sk-primary"><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div></div><h5>LOADING...</h5>',

        css: {
          backgroundColor: 'transparent',
          border: '0'
        },
        overlayCSS: {
          backgroundColor: $('html').hasClass('dark-style') ? '#000' : '#fff',
          opacity: 0.55
        }
      });
      getData(url).then(function (resp) {
        $this.parents('.card').find('.collapse').html(resp.data.view_data);
        $this.closest('.card').unblock();
        $this.closest('.card').find('.select2').each(function () {
          if (!$(this).data('select2')) {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
              dropdownParent: $this.parent()
            });
          }
        });
        if ($this.closest('.card').find('.card-alert').length) {
          $this
            .closest('.card')
            .find('.card-alert')
            .html(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button><strong>Holy grail!</strong> Your success/error message here.</div>'
            );
        }
        if(typeof initWizard == 'function'){
          initWizard();
        }
      });
    });
  }

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
