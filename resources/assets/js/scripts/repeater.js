(function ($) {
  "use strict";

  // jQuery Repeater Active
  $('.repeater').repeater({
      defaultValues: {
          'label': '',
          'type': 'text'
      },
      show: function () {
          $(this).slideDown();

          $(this).find('.select2').each(function() {
            if (!$(this).data('select2')) {
              var $this = $(this);
              $this.wrap('<div class="position-relative"></div>');
              $this.select2({
                dropdownParent: $this.parent()
              });
            }
          });
          // $('.repeaters').animate({ scrollTop: 9999 }, 'slow');
      },
      hide: function (deleteElement) {
          $(this).slideUp(deleteElement);
      },
      ready: function (setIndexes) {
          // $dragAndDrop.on('drop', setIndexes);
      },
      isFirstItemUndeletable: true
  })


})(jQuery);
