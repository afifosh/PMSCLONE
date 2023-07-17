window.initFlatPickr = function () {
  $(".flatpickr").each(function (index, element) {
    $(this).flatpickr($(this).data('flatpickr'));
  });
};

$(document).ready(function () {
  if(typeof flatpickr !== 'undefined') {
    initFlatPickr();
  }
});
