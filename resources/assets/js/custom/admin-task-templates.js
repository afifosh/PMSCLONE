/**
   * Cards Actions
   */
const collapseElementList = [].slice.call(document.querySelectorAll('.card-collapsible'));
const expandElementList = [].slice.call(document.querySelectorAll('.card-expand'));
const closeElementList = [].slice.call(document.querySelectorAll('.card-close'));

let cardDnD = document.getElementById('sortable-4');

// Collapsible card
// --------------------------------------------------------------------
if (collapseElementList) {
  collapseElementList.map(function (collapseElement) {
    collapseElement.addEventListener('click', event => {
      event.preventDefault();
      // Collapse the element
      new bootstrap.Collapse(collapseElement.closest('.card').querySelector('.collapse'));
      // Toggle collapsed class in `.card-header` element
      collapseElement.closest('.card-header').classList.toggle('collapsed');
      // Toggle class ti-chevron-down & ti-chevron-right
      Helpers._toggleClass(collapseElement.firstElementChild, 'ti-chevron-down', 'ti-chevron-right');
    });
  });
}

// Card reload (jquery)
  // --------------------------------------------------------------------

  const cardReload = $('.card-reload');
  if (cardReload.length) {
    cardReload.on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      var url = $this.parents('.card').data('href');
      $this.closest('.card').block({
        message:
          '<div class="sk-fold sk-primary"><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div></div><h5>LOADING...</h5>',

        css: {
          backgroundColor: 'transparent',
          border: '0'
        },
        overlayCSS: {
          backgroundColor: $('html').hasClass('dark-style') ? '#000' : '#fff',
          opacity: 0.55
        }
      });
      getData(url).then(function (resp) {
        $this.parents('.card').find('.collapse').html(resp.data.view_data);
        $this.closest('.card').unblock();
        $this.closest('.card').find('.select2').each(function () {
          if (!$(this).data('select2')) {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
              dropdownParent: $this.parent()
            });
          }
        });
        if ($this.closest('.card').find('.card-alert').length) {
          $this
            .closest('.card')
            .find('.card-alert')
            .html(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button><strong>Holy grail!</strong> Your success/error message here.</div>'
            );
        }
        if(typeof initWizard == 'function'){
          initWizard();
        }
      });
    });
  }

  function getData(url) {
    return $.ajax({
      url: url,
      type: 'get',
      success: function (data) {
        return data;
      }
    });
  }

  function reloadCheckItems(task_id)
  {
    $('#reload-check-items__'+task_id).trigger('click');
  }
