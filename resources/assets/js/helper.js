function saveRecord(buttonSelector,type, url, formId, errorMesage) {
    var form = document.getElementById(formId);
    var fileInput = document.getElementById('file-input');
    var data = new FormData(form);
    var isFile=false;
    if(fileInput!=null)
    {
        isFile=true;
    // data.append('file', fileInput.files);
}
var optdata=$(form).serialize();
$.each($('#'+formId+' input[type=checkbox]')
    .filter(function(idx){
        return $(this).prop('checked') === false
    }),
    function(idx, el){
        // attach matched element names to the formData with a chosen value.
        optdata += '&' + $(el).attr('name') + '=' + 0;
    }
);
if (isFile == true) {
    var data = new FormData(form);
//     var keys = Object.keys(optdata);

//     for(var i=0; i<keys.length;i++) {
//         data.append(keys[i], optdata[keys[i]]);
//     }
//  data.append('file[]', fileInput.files);
 data.append('_method', type);
    optdata = data;
}

    $.ajax({
        url: url,
        type: (isFile)?'POST':type,
        data: optdata,
        contentType: (isFile)?false:"application/x-www-form-urlencoded; charset=UTF-8",
        processData : !isFile,
        success: function (response, status) {
            if (status == "success") {
                toastr.success(response);
            }
            if(response.data!=undefined){
                return response.data;
            }
            location.reload();
        },
        error : function(jqXHR, textStatus, errorThrown) {
            onerror(jqXHR,textStatus,errorThrown,form);
        },
          beforeSend : function() {
                $(form).find(".has-error").each(function () {
                    $(this).find(".help-block").text("");
                    $(this).removeClass("has-error");
                });
                $(form).find("#alert").html("");
                    loadingButton(buttonSelector,form);
            },

            complete : function (jqXHR, textStatus) {
                    unloadingButton(buttonSelector,form)
            }
    });
}
function onerror(jqXHR,textStatus,errorThrown,form){
    try {
        var response = JSON.parse(jqXHR.responseText);
        if (typeof response == "object") {
            handleFail(response,form);
        }
        else {
            var msg = "A server side error occurred. Please try again after sometime.";

            if (textStatus == "timeout") {
                msg = "Connection timed out! Please check your internet connection";
            }
            toastr.error(msg);
        }
    }
    catch (e) {

    }
}
function handleFail(response,container) {
    if (typeof response.errors != "undefined") {
        var keys = Object.keys(response.errors);

        $(container).find(".has-error").find(".help-block").remove();
        $(container).find(".has-error").removeClass("has-error");

        if (keys.length >0) {
            for (var i = 0; i < keys.length; i++) {
                // Escape dot that comes with error in array fields
                var key = keys[i].replace(".", '\\.');
                if(key=="file"){
                    key="file-input";
                }
                var formarray = keys[i];
                // If the response has form array
                if(formarray.indexOf('.') >0){
                    var array = formarray.split('.');
                    response.errors[keys[i]] = response.errors[keys[i]];
                    key = array[0]+'[]';
                }

                var ele = $(container).find("[name='" + key + "']");

                // If cannot find by name, then find by id
                if (ele.length == 0) {
                    ele = $(container).find("#" + key);
                    if(ele.length==0){
                var ele = $(container).find("[name='" + key + "[]']");
                    }
                }

                var grp = ele.closest(".mb-3");
                $(grp).find(".help-block").remove();

                var helpBlockContainer = $(grp).find("div:first");
                if($(ele).is(':radio')){
                    helpBlockContainer = $(grp).find("div:eq(2)");
                }
                if($(ele).has('.input-group-append')){
                    helpBlockContainer = $(grp).find("div:eq(3)");
                }
                if($(ele).has('.sub-domain')){
                    helpBlockContainer = $(grp).find("div:eq(2)");
                }

                if (helpBlockContainer.length == 0) {
                    helpBlockContainer = $(grp);
                }

                helpBlockContainer.append('<div class="help-block text-danger">' + response.errors[keys[i]] + '</div>');
                $(grp).addClass("has-error");
            }

            if (keys.length > 0) {
                var element = $("[name='" + keys[0] + "']");
                if (element.length > 0) {
                    $("html, body").animate({scrollTop: element.offset().top - 150}, 200);
                }
            }
            toastr.error("Please fill all required fields.");
        }
        else {
            toastr.error(response.message);
        }
    }
    else{
        toastr.error(response.message);
        if(response.message=="CSRF token mismatch."){
            location.reload();
        }
        $("#errors").html(response.message);
        $("#errors").show();
    }
}

function loadingButton(selector,form) {
    var button = $(form).find(selector);

    var text = "Submitting...";

    if (button.width() < 20) {
        text = "...";
    }

    if (!button.is("input")) {
        button.attr("data-prev-text", button.html());
        button.text(text);
        button.prop("disabled", true);
    }
    else {
        button.attr("data-prev-text", button.val());
        button.val(text);
        button.prop("disabled", true);
    }
}

function unloadingButton(selector,form) {
    var button = $(form).find(selector);

    if (!button.is("input")) {
        button.html(button.attr("data-prev-text"));
        button.prop("disabled", false);
    }
    else {
        button.val(button.attr("data-prev-text"));
        button.prop("disabled", false);
    }
}


function ajaxModal(url, modalId) {
    $.ajax({
        url: url,
        type: "GET",
        success: function (response, status) {
            $("#" + modalId).find(".modal-content").html(response);
            $("#" + modalId).modal("show");
        },
        error: function (response) {
            var message = "";
            if
                (response.responseJSON.message == undefined) { message = errorMesage }
            else { message = response.responseJSON.message }
            toastr.error(message);
        }
    });

}

function ajaxCanvas(url, modalId) {
    $.ajax({
        url: url,
        type: "GET",
        success: function (response, status) {
            $("#" + modalId).find("div").html(response);
            $("#" + modalId).offcanvas("show");
        },
        error: function (response) {
            var message = "";
            if
                (response.responseJSON.message == undefined) { message = errorMesage }
            else { message = response.responseJSON.message }
            toastr.error(message);
        }
    });

}

function loadView(url, divId) {
    $.ajax({
        url: url,
        type: "GET",
        success: function (response, status) {
            $("#" + divId).html(response);
        },
        error: function (response) {
            var message = "";
            if
                (response.responseJSON.message == undefined) { message = errorMesage }
            else { message = response.responseJSON.message }
            toastr.error(message);
        }
    });

}
function deleteRecord(type, url, text) {
    Swal.fire({
        title: "Are you sure?",
        text: text,
        type: "warning",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        confirmButtonClass: "btn btn-primary",
        buttonsStyling: !1
    }).then(
        function (t) {
            $.ajax({
                url: url,
                type: type,
                success: function (response, status) {
                    if (status == "success") {
                        toastr.success(response);
                    }
                    location.reload();
                },
                error: function (response) {
                    var message = "";
                    if
                        (response.responseJSON.message == undefined) { message = errorMesage }
                    else { message = response.responseJSON.message }
                    toastr.error(message);
                }
            });
        });
}