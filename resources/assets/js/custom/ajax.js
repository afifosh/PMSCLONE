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
  var form = $(this).closest('form');
  var url = form.attr('action');

  var fd = new FormData(form[0]);
  $.ajax({
    type: 'POST',
    url: url,
    data: fd, // serializes the form's elements.
    success: function (data) {
      if (data.success) {
        if (data.success) {
          toast_success(data.message);
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
          //console.log(current.closest('.modal').modal("hide"));
          current.removeClass('disabled');
          if(data.data.close == 'globalModal'){
            $('#globalModal').modal('hide');
          }else if(data.data.close == 'modal'){
            current.closest('.modal').modal('hide');
          }
        }
      }
      if (data.status == false) {
        if (data.event == 'validation') {
          toast_danger(data.message);
        }
      }

      current.removeClass('disabled');
    },
    error: function (error) {
      // toast_danger(error.statusText);
      if(error.responseJSON && error.responseJSON.errors)
      {
        current.closest('form').find('.invalid').removeClass('invalid');
        current.closest('form').find('.validation-error').remove();
        $.each(error.responseJSON.errors, function (ind, val) {
          const error = '<div class="text-danger validation-error">'+ val[0] +'</div>'
          const target = $(current.closest('form').find('[name="'+ind+'"]')).length ? $(current.closest('form').find('[name="'+ind+'"]')) : $(current.closest('form').find('[name="'+ind+'[]"]'));
          target.addClass('invalid');
          if(target.hasClass('globalOfSelect2') && target.next('.select2-container').length) {
              $(error).insertAfter(target.next('.select2-container'));
          }else{
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
  // Iterate over each date field and clear its value////
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
  console.log(title);
  console.log($('#globalModalTitle').html());
  var url = $(this).data('href');
  var modal_size = $(this).data('size');
  $('.modal-dialog').removeClass('modal-lg modal-sm modal-xs modal-xl');
  if (typeof modal_size ==  'undefined' || modal_size == '') {
    modal_size = 'modal-lg';
  }
  console.log(modal_size);
  $('.modal-dialog').addClass(modal_size);
  $('#globalModalTitle').html(title);
  console.log($('#globalModalTitle').html());
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

