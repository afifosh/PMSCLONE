window.toast_danger = function (message)
{
    toastr.error(message, null, {
        positionClass: "toast-top-right",
        progressBar: true
    });
};

window.toast_success = function (message)
{
    toastr.success(message, null, {
        positionClass: "toast-top-right",
        progressBar: true
    });
};

window.toast_undo = function (url)
{
  toastr.clear();
  var message = `<div class="mb-3">Removed Successfully</div>
    <button type="button" data-href="${url}" data-call-restore class="btn btn-primary btn-sm me-2">Undo</button>`;

    toastr.success(message, null, {
        positionClass: "toast-top-right",
        closeButton: true,
        progressBar: true,
        timeOut: 30000,
    });
}

$(document).on('click', '[data-call-restore]', function(){
  var url = $(this).data('href');
  undo_deleted_resource(url);
});

window.undo_deleted_resource = function (url){
  $.ajax({
    url: url,
    type: 'GET',
    success: function(response){
      if(response.status == 'success'){
        toastr.clear();
        toastr.success('Restored Successfully');
      }
      if(response.data.event == 'functionCall'){
        // call the function whose name is in the response.data.function
        if(typeof response.data.function_params != "undefined" && response.data.function_params != null && response.data.function_params != '')
        typeof window[response.data.function] == "function" ? window[response.data.function](response.data.function_params) : null;
        else
        window[response.data.function]();
      }
    },
    error: function(error){
      toastr.clear();
      toastr.error('Something went wrong');
    }
  });
}
