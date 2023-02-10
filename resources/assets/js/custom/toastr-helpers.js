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
