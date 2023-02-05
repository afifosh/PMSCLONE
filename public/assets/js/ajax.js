$(document).ajaxStart(function(){
  $('.preloader').css({'display':''});
  $('.status').css({'display':''});
});

$(document).ajaxComplete(function(event,request,set){
  $('.preloader').css({'display':'none'});
  $('.status').css({'display':'none'});
});

$(document).ajaxError(function(event,request,set){
  $('.preloader').css({'display':'none'});
  $('.status').css({'display':'none'});
});

$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  async: true,
});
$(document).on('click', '[data-toggle="form"]', function(e) {

  e.stopPropagation();
  e.preventDefault();

  $("#formModal input, #formModal select, #formModal textarea").prop("disabled", false);

  var id = $(this).attr('data-id');
  var app_title = $(this).attr('data-app-title');
  var app_size = $(this).attr('data-size');

  id = id ? id : -1;
  app_title = app_title ? app_title : 'Add Details';

  if (app_size == 'small') {
      $('.modal-dialog').removeClass('modal-extra-large modal-lg')
  }
  else {
      $('.modal-dialog').addClass('modal-extra-large modal-lg')
  }

  var url = $(this).attr('data-href');

  $.ajax({
      type: 'GET',
      url,
      success: function(response) {
          $("#formModal .main_form").html(response.data);
          $("#formModal #formTitle").empty().append(app_title);

          //Plugins Initialization...
          pluginsIntialization();

          if (response.view_data != 'undefined') {
              if (response.view_data == 1) {
                  $("#formModal input ,#formModal select,#formModal textarea").prop("disabled", true);
                  $('#formModal').find('[type="submit"],[type="button"]').not('[data-dismiss="modal"]').hide();
              }
          }

          $("#formModal").modal("show");
      },
      error: function(e) {
          errorMessage(e.statusText);
      },
  });
});

$(document).on('click', '[data-toggle="delete_ajax"]', function(e) {

  e.stopPropagation();
  e.preventDefault();

  if (!confirm('Are you sure about deleting this ?')) return;

  var url = $(this).attr('data-url');
  var event = $(this).attr('data-event');

  $.ajax({
      type: 'GET',
      url,
      success: function(response) {
          if (response.status) {
              if (response.event == "table_reload") {
                  if (response.datatable_id != undefined && response.datatable_id != null && response.datatable_id != '') {
                      $('#' + response.datatable_id).DataTable().ajax.reload();
                  }
                  else {
                      $('#dataTableBuilder').DataTable().ajax.reload();
                  }
              }
              showMessage(response.message);
          }
          else {
              errorMessage(response.message);
          }
      },
      error: function(e) {
          errorMessage(e.statusText);
      },
  });
});

$(document).on('click', '[data-toggle="tab"]', function(e) {

  var target = $(this).attr('data-target');
  var url = $(this).attr('data-href');

  if (!target || !url) return;

  e.stopPropagation();
  e.preventDefault();

  $.ajax({
      type: 'GET',
      url,
      success: function(response) {
          if (target) {
              $(target).html(response.data);
              $(this).addClass('active').parent().siblings().children().removeClass('active');
          }
      },
      error: function(e) {
          errorMessage(e.statusText);
      },
  });
});

$(document).on('click', '[data-toggle="tabajax"]', function(e) {

  e.stopPropagation();
e.preventDefault();

  var $this = $(this);
  var loadurl = $this.attr('data-href');
var	targ = $this.attr('data-target');
var	contenttype = '0';
var	datatype = '0';
var	id = $this.id || '';

  $this.addClass('active').parent().siblings().children().removeClass('active');

  if ($this.attr('data-contenttype')) {
  contenttype = $this.attr('data-contenttype')
}

if ($this.attr('data-datatype')) {
  datatype = $this.attr('data-datatype')
}

if(contenttype != '0' && datatype != '0') {
  $.ajax({
          url: loadurl,
          type: "GET",
          contentType: contenttype,
          dataType: datatype,
          success: function(data) {
              if (data.status != undefined || data.status != null) {
                  if (data.status == false) {
                      if (data.message != undefined || data.message != null) {
                          errorMessage(data.message);
                          return false;
                      }
                  }
              }

              var newElement = $('<div class="bubble">' + data + '</div>');
              $(targ).attr('data-parent',id).empty().append(newElement);

              var inner_active_tab = window.inner_active_tab || '';

              if (inner_active_tab != '') {
                  var select_active = inner_active_tab;
                  inner_active_tab = '';
                  $(document).find("#" + select_active).trigger('click');
              }

              pluginsIntialization();

          }
      });
}
  else {
      $.get(loadurl, function(data) {
          if (data.status != undefined || data.status != null) {
              if (data.status == false) {
                  if (data.message != undefined || data.message != null) {
                      errorMessage(data.message);
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
              $(document).find("#" + select_active).trigger('click');
          }

          pluginsIntialization();

      });
  }
});

$(document).on('click', '[data-form="ajax"]', function (e) {
  e.preventDefault();
  var current = $(this);
  current.addClass('disabled');
  var form = $(this).closest('form');
  var url = form.attr('action');

  var fd = new FormData(form[0]);
  $.ajax({
      type: "POST",
      url: url,
      data: fd, // serializes the form's elements.
      success: function (data) {
          if (data.status == true) {
              if (data.event == "submited") {
                  showMessage(data.message);
                  if (data.datatable_id != undefined && data.datatable_id != null && data.datatable_id != '') {
                      $('#' + data.datatable_id).DataTable().ajax.reload();
                  }
                  else {
                      $('#dataTableBuilder').DataTable().ajax.reload();
                  }
                  //console.log(current.closest('.modal').modal("hide"));
                  current.removeClass('disabled');
                  current.closest('.modal').modal("hide");

              }

              if (data.triger_event == true) {
                  if (data.triger_button != undefined) {
                      $('#' + data.triger_button).trigger('click');
                  } else {
                      //$('#encounter_summary_tab').trigger('click');
                      if (data.soap_tab != undefined) {
                          setTimeout(function() {
                              $('#o-section-tab').trigger('click');
                          }, 2000)
                      }
                  }
              }
              if (data.reloadList != undefined) {
                  if (data.reloadList == true){
                      if (data.triggerFocusIn != undefined) {
                          // console.log('working');
                          $('.'  + data.triggerFocusIn).trigger('focus');
                      }
                  }
              }
          }

          if (data.status == false) {
              if (data.event == 'validation') {
                  errorMessage(data.message);
              }
          }

          current.removeClass('disabled');
      },
      error: function (error) {
          errorMessage(error.statusText);
          current.removeClass('disabled');
      },
      cache: false,
      contentType: false,
      processData: false,
  });

});
$('.close_modal').on('click', function() {
  $(this).closest('.modal').modal("hide");
});
