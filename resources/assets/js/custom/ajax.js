$(document).ajaxStart(function () {
  $('.preloader').css({ display: '' });
  $('.status').css({ display: '' });
});

$(document).ajaxComplete(function (event, request, set) {
  $('.preloader').css({ display: 'none' });
  $('.status').css({ display: 'none' });
});

$(document).ajaxError(function (event, request, set) {
  $('.preloader').css({ display: 'none' });
  $('.status').css({ display: 'none' });
});

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  async: true
});
$(document).on('click', '[data-toggle="form"]', function (e) {
  e.stopPropagation();
  e.preventDefault();

  $('#formModal input, #formModal select, #formModal textarea').prop('disabled', false);

  var id = $(this).attr('data-id');
  var app_title = $(this).attr('data-app-title');
  var app_size = $(this).attr('data-size');

  id = id ? id : -1;
  app_title = app_title ? app_title : 'Add Details';

  if (app_size == 'small') {
    $('.modal-dialog').removeClass('modal-extra-large modal-lg');
  } else {
    $('.modal-dialog').addClass('modal-extra-large modal-lg');
  }

  var url = $(this).attr('data-href');

  $.ajax({
    type: 'GET',
    url,
    success: function (response) {
      $('#formModal .main_form').html(response.data);
      $('#formModal #formTitle').empty().append(app_title);

      //Plugins Initialization...
      // pluginsIntialization();

      if (response.view_data != 'undefined') {
        if (response.view_data == 1) {
          $('#formModal input ,#formModal select,#formModal textarea').prop('disabled', true);
          $('#formModal').find('[type="submit"],[type="button"]').not('[data-dismiss="modal"]').hide();
        }
      }

      $('#formModal').modal('show');
    },
    error: function (e) {
      toast_danger(e.statusText);
    }
  });
});

$(document).on('click', '[data-toggle="delete_ajax"]', function (e) {
  e.stopPropagation();
  e.preventDefault();

  if (!confirm('Are you sure about deleting this ?')) return;

  var url = $(this).attr('data-url');
  var event = $(this).attr('data-event');

  $.ajax({
    type: 'GET',
    url,
    success: function (response) {
      if (response.status) {
        if (response.event == 'table_reload') {
          if (response.datatable_id != undefined && response.datatable_id != null && response.datatable_id != '') {
            $('#' + response.datatable_id)
              .DataTable()
              .ajax.reload();
          } else {
            $('#dataTableBuilder').DataTable().ajax.reload();
          }
        }
        toast_success(response.message);
      } else {
        toast_danger(response.message);
      }
    },
    error: function (e) {
      toast_danger(e.statusText);
    }
  });
});

$(document).on('click', '[data-toggle="tab"]', function (e) {
  var target = $(this).attr('data-target');
  var url = $(this).attr('data-href');

  if (!target || !url) return;

  e.stopPropagation();
  e.preventDefault();

  $.ajax({
    type: 'GET',
    url,
    success: function (response) {
      if (target) {
        $(target).html(response.data);
        $(this).addClass('active').parent().siblings().children().removeClass('active');
      }
    },
    error: function (e) {
      toast_danger(e.statusText);
    }
  });
});

$(document).on('click', '[data-toggle="tabajax"]', function (e) {
  e.stopPropagation();
  e.preventDefault();

  var $this = $(this);
  var loadurl = $this.attr('data-href');
  var targ = $this.attr('data-target');
  var contenttype = '0';
  var datatype = '0';
  var id = $this.id || '';

  $this.addClass('active').parent().siblings().children().removeClass('active');

  if ($this.attr('data-contenttype')) {
    contenttype = $this.attr('data-contenttype');
  }

  if ($this.attr('data-datatype')) {
    datatype = $this.attr('data-datatype');
  }

  if (contenttype != '0' && datatype != '0') {
    $.ajax({
      url: loadurl,
      type: 'GET',
      contentType: contenttype,
      dataType: datatype,
      success: function (data) {
        if (data.status != undefined || data.status != null) {
          if (data.status == false) {
            if (data.message != undefined || data.message != null) {
              toast_danger(data.message);
              return false;
            }
          }
        }

        var newElement = $('<div class="bubble">' + data + '</div>');
        $(targ).attr('data-parent', id).empty().append(newElement);

        var inner_active_tab = window.inner_active_tab || '';

        if (inner_active_tab != '') {
          var select_active = inner_active_tab;
          inner_active_tab = '';
          $(document)
            .find('#' + select_active)
            .trigger('click');
        }

        // pluginsIntialization();
      }
    });
  } else {
    $.get(loadurl, function (data) {
      if (data.status != undefined || data.status != null) {
        if (data.status == false) {
          if (data.message != undefined || data.message != null) {
            toast_danger(data.message);
            return false;
          }
        }
      }

      var newElement = $('<div class="bubble">' + data + '</div>');
      $(targ).attr('data-parent', id).empty().append(newElement);

      var inner_active_tab = window.inner_active_tab || '';

      if (inner_active_tab != '') {
        var select_active = inner_active_tab;
        inner_active_tab = '';
        $(document)
          .find('#' + select_active)
          .trigger('click');
      }

      // pluginsIntialization();
    });
  }
});

$(document).on('click', '[data-form="ajax-form"]', function (e) {
  e.preventDefault();
  var current = $(this);
  current.addClass('disabled');
  current.prepend('<span class="spinner-border me-1" role="status" aria-hidden="true"></span>');
  // current.closest('form').find('[data-form="ajax-form"]').addClass('disabled');
  current.addClass('disabled');
  const preAjaxAction = current.attr('data-preAjaxAction');
  if (typeof window[preAjaxAction] == "function") {
    const params = current.attr('data-preAjaxParams');
    window[preAjaxAction](params);
  }
  var form = current.data('form-id') ? $('#'+current.data('form-id')) : current.closest('form');
  var url = form.attr('action');
  var fd = new FormData(form[0]);

//   const form = document.querySelector('form');
// const formData = new FormData(form);

const inputs = form[0].querySelectorAll('input, select, textarea');
for (const input of inputs) {
  if (input.type === 'checkbox') {
    if (!input.checked) {
      fd.append(input.name, '');
    }
  } else if (input.type === 'radio') {
    if (input.checked) {
      fd.append(input.name, input.value);
    }
  } else {
    fd.append(input.name, input.value);
  }
}


  $.ajax({
    type: 'POST',
    url: url,
    data: fd, // serializes the form's elements.

    success: function (data, textStatus, xhr) {
      // remove any validation messages
      form.find('.invalid').removeClass('invalid');
      form.find('.validation-error').remove();

      // check for a filename
      var filename = "";
      var disposition = xhr.getResponseHeader('Content-Disposition');
      if (disposition && disposition.indexOf('attachment') !== -1) {
        var filename = "";
        var disposition = xhr.getResponseHeader('Content-Disposition');
        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
        var matches = filenameRegex.exec(disposition);
        if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
        var type = xhr.getResponseHeader('Content-Type');

        var blob = new Blob([data], { type: type });
        if (typeof window.navigator.msSaveBlob !== 'undefined') {
            // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
            window.navigator.msSaveBlob(blob, filename);
        } else {
            var URL = window.URL || window.webkitURL;
            var downloadUrl = URL.createObjectURL(blob);

            if (filename) {
                // use HTML5 a[download] attribute to specify filename
                var a = document.createElement("a");
                // safari doesn't support this yet
                if (typeof a.download === 'undefined') {
                    window.location = downloadUrl;
                } else {
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                }
            } else {
                window.location = downloadUrl;
            }

            setTimeout(function() { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
        }
      }
      if (data.success) {
        if (data.success) {
          if(data.message){
            toast_success(data.message);
          }

          if (data.data.event == 'table_reload' && data.data.table_id != undefined && data.data.table_id != null && data.data.table_id != '') {
            $('#' + data.data.table_id)
              .DataTable()
              .ajax.reload();
          }
          if(data.data.event == 'page_reload'){
            setTimeout(function() { // wait for 1 second
              location.reload(); // then reload the page
            }, 1000);
          }
          if(data.data.event == 'redirect'){
            setTimeout(function() { // wait for 1 second
              window.location.href = data.data.url;
            }, 1000);
          }
          if(data.data.event == 'functionCall'){
            // call the function whose name is in the data.data.function
            typeof window[data.data.function] == "function" ?
              (typeof data.data.function_params != "undefined" ? window[data.data.function](data.data.function_params)
                : window[data.data.function]())
              : null;
          }
          //console.log(current.closest('.modal').modal("hide"));
          current.removeClass('disabled');
          current.find('.spinner-border').remove();
          if(data.data.close == 'globalModal'){
            $('#globalModal').modal('hide');
          }else if(data.data.close == 'modal'){
            current.closest('.modal').modal('hide');
          }
          if(data.data.JsMethods != undefined && data.data.JsMethods != null && data.data.JsMethods != ''){
            for(var i = 0; i < data.data.JsMethods.length; i++){
              eval(data.data.JsMethods[i]+'()');
            }
          }
        }
      }
      if (data.status == false) {
        if (data.event == 'validation') {
          toast_danger(data.message);
        }
      }
    },
    error: function (error) {
      current.removeClass('disabled');
      current.find('.spinner-border').remove();
      // toast_danger(error.statusText);
      if(error.responseJSON && error.responseJSON.errors)
      {
        form.find('.invalid').removeClass('invalid');
        form.find('.validation-error').remove();
        $.each(error.responseJSON.errors, function (ind, val) {
          const error = '<div class="text-danger validation-error">'+ val[0] +'</div>'


          var tsname1 = ind.split('.').map(function(str) {
            return /\d+/.test(str) ? '[' + str + ']' : str;
          }).join('');
          var tsname = tsname1;
          transformedName = tsname1.replace(/\[\d*\]$/, '[]');

          var ts2 = ind.replace(/\.(\w+)/g, '[$1]');
          var t = ts2;
          var ts3 = t.replace(/\[\d+\]$/, '[]');

          if($(form.find('[name="'+ind+'"]')).length){
            var target = $(form.find('[name="'+ind+'"]'));
          }else if($(form.find('[name="'+ind+'[]"]')).length){
            var target = $(form.find('[name="'+ind+'[]"]'));
          }else if($(form.find('[name="'+ind+'['+ind+']"]')).length){
            var target = $(form.find('[name="'+ind+'['+ind+']"]'));
          }else if($(form.find('[name="'+tsname+'"]')).length){
            var target = $(form.find('[name="'+tsname+'"]'));
          }else if($(form.find('[name="'+tsname+'[]"]')).length){
            var target = $(form.find('[name="'+tsname+'[]"]'));
          }else if($(form.find('[name="'+transformedName+'"]')).length){
            var target = $(form.find('[name="'+transformedName+'"]'));
          }else if($(form.find('[name="'+transformedName+'[]"]')).length){
            var target = $(form.find('[name="'+transformedName+'[]"]'));
          }else if($(form.find('[name="'+ts2+'"]')).length){
            var target = $(form.find('[name="'+ts2+'"]'));
          }else if($(form.find('[name="'+ts2+'[]"]')).length){
            var target = $(form.find('[name="'+ts2+'[]"]'));
          }else if($(form.find('[name="'+ts3+'"]')).length){
            var target = $(form.find('[name="'+ts3+'"]'));
          }
          target.addClass('invalid');
          if((target.hasClass('globalOfSelect2') || target.hasClass('select2User') || target.hasClass('select2')) && target.next('.select2-container').length) {
              $(error).insertAfter(target.next('.select2-container'));
          }else if(target.parent().hasClass('input-group')){
            target.siblings().addClass('invalid');
           $(error).insertAfter( target.parent() );
          }
          else{
            target.after(error);
          }
        });
      }

        // toast_danger(val[0]);
      current.removeClass('disabled');
    },
    cache: false,
    contentType: false,
    processData: false
  });
});
$('.close_modal').on('click', function () {
  $(this).closest('.modal').modal('hide');
});


$(document).on('click', '.clear-form' , function(){
  $(this).closest('form').find('select').val('').trigger('change').select2("close");
  $(this).closest('form').find('select').find('option').filter('[selected]').removeAttr('selected');

  var dateFields = $(".flatpickr");
  // Iterate over each date field and clear its value/
  $(".flatpickr").attr('value', '');
  dateFields.each(function(index, element) {
    var flatpickrInstance = element._flatpickr;
    flatpickrInstance.clear();
  });
})
// Delete Record
$(document).on('click', '[data-toggle="ajax-delete"]', function () {
  dtrModal = $('.dtr-bs-modal.show');
  var url = $(this).data('href');

  // hide responsive modal in small screen
  if (dtrModal.length) {
    dtrModal.modal('hide');
  }

  // sweetalert for confirmation of delete
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    customClass: {
      confirmButton: 'btn btn-primary me-3',
      cancelButton: 'btn btn-label-secondary'
    },
    buttonsStyling: false
  }).then(function (result) {
    if (result.value) {
      // delete the data
      $.ajax({
        type: 'DELETE',
        url: url,
        success: function (response) {
          if (response.success) {
            if(!response.data.disable_alert)
              toast_success(response.message)
            if (response.data.event == 'table_reload') {
              if (response.data.table_id != undefined && response.data.table_id != null && response.data.table_id != '') {
                $('#' + response.data.table_id).DataTable().ajax.reload();
              } else {
                $('#dataTableBuilder').DataTable().ajax.reload();
              }
            }
            if(response.data.event == 'page_reload'){
              setTimeout(function() { // wait for 1 second
                location.reload(); // then reload the page
              }, 1000);
            }
            if(response.data.event == 'redirect'){
              setTimeout(function() { // wait for 1 second
                window.location.href = response.data.url;
              }, 1000);
            }
            if(response.data.event == 'functionCall'){
              // call the function whose name is in the response.data.function
              if(typeof response.data.function_params != "undefined" && response.data.function_params != null && response.data.function_params != '')
              typeof window[response.data.function] == "function" ? window[response.data.function](response.data.function_params) : null;
              else
              typeof window[response.data.function] == "function" ? window[response.data.function]() : null;
            }
          } else {
            toast_danger(response.message)
          }
        },
        error: function (error) {
          console.log(error);
        }
      });
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      // Swal.fire({
      //   title: 'Cancelled',
      //   text: 'The User is not deleted!',
      //   icon: 'error',
      //   customClass: {
      //     confirmButton: 'btn btn-success'
      //   }
      // });
    }
  });
});
window.initModalSelect2 = function(){
  if(typeof select2 == 'undefined')
  {
    return true;
  }
  $('.globalOfSelect2').select2({
    dropdownParent: $('#globalModal')
  });
  // select2 with remote data for global modal
  $('.globalOfSelect2Remote').each(function (){
    $(this).select2({
      ajax: {
        url: $(this).data('url'),
        dataType: 'json',
        delay: 500,
        data: function (params) {
          return {
            q: params.term,
            page: params.page || 1
          };
        },
        processResults: function(data, params) {
          return {
              results: data.data,
              pagination: {
                  more: data.next_page_url ? true : false
              }
          };
        }
      },
      placeholder: $(this).data('placeholder'),
      minimumInputLength: 0,
      dropdownParent: $('#globalModal')
    });
  })
  // END select2 with remote data for global modal

  // select2User with remote data for global modal
  $('.globalOfSelect2UserRemote').each(function (){
    $(this).select2({
      ajax: {
        url: $(this).data('url'),
        dataType: 'json',
        delay: 500,
        data: function (params) {
          return {
            q: params.term,
            page: params.page || 1
          };
        },
        processResults: function(data, params) {
          return {
              results: data.data,
              pagination: {
                  more: data.next_page_url ? true : false
              }
          };
        }
      },
      templateResult: renderRemoteUser,
      templateSelection: renderRemoteSelectedUser,
      escapeMarkup: function (es) {
        return es;
      },
      placeholder: $(this).data('placeholder'),
      minimumInputLength: 0,
      dropdownParent: $('#globalModal')
    });
  })
  function renderRemoteUser(option) {
    if (!option.id) {
      return option.text;
    }
    return '<div class="d-flex justify-content-start align-items-center user-name"><div class="avatar-wrapper"><div class="avatar avatar-sm me-3"><img src="'+option.avatar+'"></div></div><div class="d-flex flex-column"><span class="text-body text-truncate"><span class="fw-semibold">'+option.full_name+'</span></span><small class="text-muted">'+option.text+'</small></div></div>';
  }
  function renderRemoteSelectedUser(option) {
    if (option.full_name == undefined || option.full_name == null || option.full_name == '') {
      return option.text;
    }
    return option.full_name
  }
  // END select2User with remote data for global modal

  var UsersSelect2 = $('.globalOfSelect2User');

  if (UsersSelect2.length) {
    // custom template to render icons
    function renderUser(option) {
      if (!option.id) {
        return option.text;
      }
      return '<div class="d-flex justify-content-start align-items-center user-name"><div class="avatar-wrapper"><div class="avatar avatar-sm me-3"><img src="'+$(option.element).data('avatar')+'"></div></div><div class="d-flex flex-column"><span class="text-body text-truncate"><span class="fw-semibold">'+$(option.element).data('full_name')+'</span></span><small class="text-muted">'+option.text+'</small></div></div>';
    }
    function renderSelectedUser(option) {
      if (!$(option.element).data('full_name')) {
        return option.text;
      }
      return $(option.element).data('full_name')
    }
    UsersSelect2.select2({
      templateResult: renderUser,
      templateSelection: renderSelectedUser,
      escapeMarkup: function (es) {
        return es;
      },
      dropdownParent: $('#globalModal')
    });
  }
}
// ajax OffCanvax
$(document).on('click', '[data-toggle="ajax-modal"]', function () {
  var title = $(this).data('title');
  var url = $(this).data('href');
  var modal_size = $(this).data('size');
  $('.modal-dialog').removeClass('modal-lg modal-sm modal-xs modal-xl');
  if (typeof modal_size ==  'undefined' || modal_size == '') {
    modal_size = 'modal-lg';
  }
  $('.modal-dialog').addClass(modal_size);
  $('#globalModalTitle').html(title);
  $.ajax({
    type: 'get',
    url: url,
    success: function (response) {
      $('#globalModalTitle').html(title);
      $('#globalModalBody').html(response.data.view_data);
      initModalSelect2();
      if(typeof initFlatPickr != 'undefined'){
        initFlatPickr();
      }
      if(response.data.JsMethods != undefined && response.data.JsMethods != null && response.data.JsMethods != ''){
        for(var i = 0; i < response.data.JsMethods.length; i++){
          eval(response.data.JsMethods[i]+'()');
        }
      }
      $('#globalModal').modal('show');
    },
  });
});

$(document).on('change', '[data-updateOptions="ajax-options"]', function () {
  var url = $(this).data('href');
  var id = $(this).val();
  var target = $(this).data('target');
  if(id == 'NaN' || id == '')
  {
    // $(target).empty();
    return true;
  }
  $.ajax({
    type: 'get',
    url: url,
    data: {
      'id' : id
    },
    success: function (response) {
      $(target).empty();
      const map = new Map(Object.entries(response.data.data));
      for (const [key, value] of map) {
        if(key == ''){
          $(target).prepend($('<option>', {
              value: key,
              text: value
          })).val('');
        }else{
          $(target).append($('<option>', {
              value: key,
              text: value
          }));
        }

      }
      initModalSelect2();
      if(typeof initFlatPickr != 'undefined'){
        initFlatPickr();
      }
    },
  });
});

