window.initFlatPickr = function () {
  $(".flatpickr").each(function (index, element) {
    $(this).flatpickr($(this).data('flatpickr'));

    var config =  $(this).data('flatpickr');

    if(typeof config === 'undefined') {
      config = {};
    }

    config.allowInput = true;

    $(this).flatpickr(config);
  });
};

$(document).ready(function () {
  if(typeof flatpickr !== 'undefined') {
    initFlatPickr();
  }
});
