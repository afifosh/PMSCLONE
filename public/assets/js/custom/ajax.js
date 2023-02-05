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
          //console.log(current.closest('.modal').modal("hide"));
          current.removeClass('disabled');
          if(data.data.close == 'globalOffCanvas'){
            globalOffCanvas.hide();
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
      $.each(error.responseJSON.errors, function (ind, val) {
        current.closest('form').find('[name="'+ind+'"]').addClass('invalid');
        toast_danger(val[0]);
      });
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
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: 'The user has been deleted!',
              customClass: {
                confirmButton: 'btn btn-success'
              }
            });
            if (response.data.event == 'table_reload') {
              if (response.data.table_id != undefined && response.data.table_id != null && response.data.table_id != '') {
                $('#' + response.data.table_id).DataTable().ajax.reload();
              } else {
                $('#dataTableBuilder').DataTable().ajax.reload();
              }
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
      Swal.fire({
        title: 'Cancelled',
        text: 'The User is not deleted!',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-success'
        }
      });
    }
  });
});
window.OffcanvasSelect2 = function(){
  $('.globalOfSelect2').select2({
    dropdownParent: $('#globalOffcanvas')
});
}
// ajax OffCanvax
$(document).on('click', '[data-toggle="ajax-offcanvas"]', function () {
  var title = $(this).data('title');
  var url = $(this).data('href');
  $.ajax({
    type: 'get',
    url: url,
    success: function (response) {
      console.log(response);
    $('#globalOffcanvasTitle').html(title);
    $('#globalOffcanvasBody').html(response.data.view_data);
    window.globalOffCanvas = new bootstrap.Offcanvas($('#globalOffcanvas'))
    globalOffCanvas.show();
    OffcanvasSelect2();
    },
  });
});
