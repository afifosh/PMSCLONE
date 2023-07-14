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
  var message = `<div class="mb-3">Removed Successfully</div>
    <button type="button" onclick="undo_deleted_resource(${url})"; class="btn btn-primary btn-sm me-2">Undo</button>`;

    toastr.success(message, null, {
        positionClass: "toast-top-right",
        progressBar: true,
        showDuration: "30000"
    });
}

window.undo_deleted_resource = function (url){
  $.ajax({
    url: url,
    type: 'GET',
    success: function(response){
      toastr.clear();
      toastr.success('Resource restored successfully');
    },
    error: function(error){
      toastr.clear();
      toastr.error('Something went wrong');
    }
  });
}
