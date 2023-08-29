window.initSelect2 = function(){
  $('.select2Remote').each(function (){
    $(this).select2({
      ajax: {
        url: $(this).data('url'),
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            search: params.term,
            page: params.current_page || 1
          };
        }
      },
      placeholder: $(this).data('placeholder'),
      minimumInputLength: 3
    });
  })
  $('.select2').select2();

  var UsersSelect2 = $('.select2User');

  if (UsersSelect2.length) {
    // custom template to render icons
    function renderUser(option) {
      if (!option.id) {
        return option.text;
      }
      return '<div class="d-flex justify-content-start align-items-center user-name"><div class="avatar-wrapper"><div class="avatar avatar-sm me-3"><img src="'+$(option.element).data('avatar')+'"></div></div><div class="d-flex flex-column"><span class="text-body text-truncate"><span class="fw-semibold">'+$(option.element).data('full_name')+'</span></span><small class="text-muted">'+option.text+'</small></div></div>';
    }
    function renderSelectedUser(option) {
      if (!$(option.element).data('full_name')) {
        return option.text;
      }
      return $(option.element).data('full_name')
    }
    UsersSelect2.select2({
      templateResult: renderUser,
      templateSelection: renderSelectedUser,
      escapeMarkup: function (es) {
        return es;
      }
    });
  }
}
$(document).ready(function () {
  initSelect2();
});
