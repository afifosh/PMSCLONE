window.initSelect2 = function(){
  $('.select2').select2();
  // select2 with remote data
  $('.select2Remote').each(function (){
    $(this).select2({
      ajax: {
        url: $(this).data('url'),
        dataType: 'json',
        delay: 500,
        data: function (params) {
          return {
            q: params.term,
            page: params.page || 1
          };
        },
        processResults: function(data, params) {
          return {
              results: data.data,
              pagination: {
                  more: data.next_page_url ? true : false
              }
          };
        }
      },
      placeholder: $(this).data('placeholder'),
      minimumInputLength: 0
    });
  })
  // END select2 with remote data

  // select2User with remote data
  $('.select2UserRemote').each(function (){
    $(this).select2({
      ajax: {
        url: $(this).data('url'),
        dataType: 'json',
        delay: 500,
        data: function (params) {
          return {
            q: params.term,
            page: params.page || 1
          };
        },
        processResults: function(data, params) {
          return {
              results: data.data,
              pagination: {
                  more: data.next_page_url ? true : false
              }
          };
        }
      },
      templateResult: renderRemoteUser,
      templateSelection: renderRemoteSelectedUser,
      escapeMarkup: function (es) {
        return es;
      },
      placeholder: $(this).data('placeholder'),
      minimumInputLength: 0,
      cache: true
    });
  })
  function renderRemoteUser(option) {
    if (!option.id) {
      return option.text;
    }
    return '<div class="d-flex justify-content-start align-items-center user-name"><div class="avatar-wrapper"><div class="avatar avatar-sm me-3"><img src="'+option.avatar+'"></div></div><div class="d-flex flex-column"><span class="text-body text-truncate"><span class="fw-semibold">'+option.full_name+'</span></span><small class="text-muted">'+option.text+'</small></div></div>';
  }
  function renderRemoteSelectedUser(option) {
    if (option.full_name == undefined || option.full_name == null || option.full_name == '') {
      return option.text;
    }
    return option.full_name
  }
  // END select2User with remote data for global modal

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
