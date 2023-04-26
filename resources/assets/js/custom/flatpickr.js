window.initFlatPickr = function () {
  $(".flatpickr").each(function (index, element) {
    $(this).flatpickr($(this).data('flatpickr'));
  });
};
