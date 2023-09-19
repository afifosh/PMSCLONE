@extends('admin.layouts/layoutMaster')
@section('page-style')
<link href="{{asset('assets/css/invoices/bootstrap.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/css/invoices/vendor.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />


<!--ICONS-->
<link rel="stylesheet" href="{{asset('assets/css/invoices/styles.css')}}">

<!--THEME STYLE-->
<!--use the default theme for all external pages (e.g. proposals, cotracts etc) -->
    <link rel="stylesheet" href="{{asset('assets/css/invoices/style.css')}}">

<!--USERS CUSTON CSS FILE-->
{{-- <link rel="stylesheet" href="{{asset('assets/css/invoices/custom.css')}}"> --}}

<!--PRINTING CSS-->
<link rel="stylesheet" href="{{asset('assets/css/invoices/print.css')}}">

@endsection
@section('page-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script type="text/javascript">
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  async: true
});
  //name space & settings
  NX = (typeof NX == 'undefined') ? {} : NX;
  NXJS = (typeof NXJS == 'undefined') ? {} : NXJS;
  NXLANG = (typeof NXLANG == 'undefined') ? {} : NXLANG;
  NXINVOICE = (typeof NXINVOICE == 'undefined') ? {} : NXINVOICE;
  NXDOC = (typeof NXDOC == 'undefined') ? {} : NXDOC;
  NX.data = (typeof NX.data == 'undefined') ? {} : NX.data;

  NXINVOICE.DATA = {};
  NXINVOICE.DOM = {};
  NXINVOICE.CALC = {};

  //variables
  NX.site_url = "{{url('/')}}";
  NX.system_type = "tenant"; //landlord/tenant/frontend
  NX.site_page_title = "Grow More Invoices";
  NX.csrf_token = "{{csrf_token()}}";
  NX.system_language = "english";
  NX.date_format = "m-d-Y";
  NX.date_picker_format = "mm-dd-yyyy";
  NX.date_moment_format = "MM-DD-YYYY";
  NX.upload_maximum_file_size = "5000";
  NX.settings_system_currency_symbol = "$";
  NX.settings_system_decimal_separator =
      ".";
  NX.settings_system_thousand_separator =
      ",";
  NX.settings_system_currency_position = "left";
  NX.show_action_button_tooltips = "1";
  NX.notification_position = "bottomLeft";
  NX.notification_error_duration = "5000";
  NX.notification_success_duration = "3000";
  NX.session_login_popup = "enabled";


  //javascript console debug modes
  NX.debug_javascript = "";

  //popover template
  NX.basic_popover_template = '<div class="popover card-popover" role="tooltip">' +
      '<span class="popover-close" onclick="$(this).closest(\'div.popover\').popover(\'hide\');" aria-hidden="true">' +
      '<i class="ti-close"></i></span>' +
      '<div class="popover-header"></div><div class="popover-body" id="popover-body"></div></div>';

  //lang - used in .js files
  NXLANG.delete_confirmation = "Delete Confirmation";
  NXLANG.are_you_sure_delete = "Are you sure you want to delete this item?";
  NXLANG.cancel = "Cancel";
  NXLANG.continue = "Continue";
  NXLANG.file_too_big = "File is too big";
  NXLANG.maximum = "Maximum";
  NXLANG.generic_error = "An error was encountered processing your request";
  NXLANG.drag_drop_not_supported = "Your browser does not support drag and drop";
  NXLANG.use_the_button_to_upload = "Use the button to upload";
  NXLANG.file_type_not_allowed = "File type is not allowed";
  NXLANG.cancel_upload = "Cancel upload";
  NXLANG.remove_file = "Remove file";
  NXLANG.maximum_upload_files_reached = "Maximum allowed files has been reached";
  NXLANG.upload_maximum_file_size = "lang.upload_maximum_file_size";
  NXLANG.upload_canceled = "Upload cancelled";
  NXLANG.are_you_sure = "Are you sure?";
  NXLANG.image_dimensions_not_allowed = "Images dimensions are not allowed";
  NXLANG.ok = "Ok";
  NXLANG.cancel = "Cancel";
  NXLANG.close = "Close";
  NXLANG.system_default_category_cannot_be_deleted =
      "This is a system default category and cannot be deleted";
  NXLANG.default_category = "Default Catagory";
  NXLANG.select_atleast_one_item = "You must select at least one item";
  NXLANG.invalid_discount = "The discount is not valid";
  NXLANG.add_lineitem_items_first = "First add invoice products";
  NXLANG.fixed = "Fixed";
  NXLANG.percentage = "Percentage";
  NXLANG.action_not_completed_errors_found = "The request could not be completed";
  NXLANG.selected_expense_is_already_on_invoice =
      "One of the selected expenses is already on the invoice";
  NXLANG.please_wait = "Please wait...";
  NXLANG.invoice_time_unit = "Time";
  NXLANG.dimensions_default_unit = "m2"
  NXLANG.tax = "Tax";

  //arrays to use generically
  NX.array_1 = [];
  NX.array_2 = [];
  NX.array_3 = [];
  NX.array_4 = [];
</script>
<script src="{{asset('assets/js/invoices/head.js')}}"></script>
{{-- <script src="http://grow-crm.pk/public/vendor/js/vendor.footer.js?v=2023-06-15"></script> --}}
<script src="{{asset('assets/js/invoices/vendor.footer.js')}}"></script>

<!--nextloop.core.js-->
<script src="{{asset('assets/js/invoices/ajax.js')}}"></script>

<!--MAIN JS - AT END-->
<script src="{{asset('assets/js/invoices/boot.js')}}"></script>

<!--EVENTS-->
<script src="{{asset('assets/js/invoices/events.js')}}"></script>

<!--CORE-->
<script src="{{asset('assets/js/invoices/app.js')}}"></script>

<!--BILLING-->
<script src="{{asset('assets/js/invoices/billing.js')}}"></script>
@endsection
@section('vendor-script')
<script src="{{asset('assets/js/invoices/header.js')}}"></script>
@endsection
@section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.invoices.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!--stats panel-->
    @if(auth()->user()->is_team)
    <div class="stats-wrapper" id="invoices-stats-wrapper">
    @include('misc.list-pages-stats')
    </div>
    @endif
    <!--stats panel-->


    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <!--invoices table-->
            @include('pages.invoices.components.table.wrapper')
            <!--invoices table-->
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->
<!--modal-->
<div class="modal" role="dialog" aria-labelledby="foo" id="commonModal" {!! runtimeAllowCloseModalOptions() !!}>
  <div class="modal-dialog" id="commonModalContainer">
      <form action="" method="post" id="commonModalForm" class="form-horizontal">
          <div class="modal-content">
              <div class="modal-header" id="commonModalHeader">
                  <h4 class="modal-title" id="commonModalTitle"></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"
                      id="commonModalCloseIcon">
                      <i class="ti-close"></i>
                  </button>
              </div>
              <!--optional button for when header is hidden-->
              <span class="close x-extra-close-icon" data-dismiss="modal" aria-hidden="true"
                  id="commonModalExtraCloseIcon">
                  <i class="ti-close"></i>
              </span>
              <div class="modal-body min-h-200" id="commonModalBody">
                  <!--dynamic content here-->
              </div>
              <div class="modal-footer" id="commonModalFooter">
                  <button type="button" id="commonModalCloseButton" class="btn btn-rounded-x btn-secondary waves-effect text-left" data-dismiss="modal">{{ cleanLang(__('lang.close')) }}</button>
                  <button type="submit" id="commonModalSubmitButton"
                      class="btn btn-rounded-x btn-danger waves-effect text-left" data-url="" data-loading-target=""
                      data-ajax-type="POST" data-on-start-submit-button="disable">{{ cleanLang(__('lang.submit')) }}</button>
              </div>
          </div>
      </form>
  </div>
</div>
<!--notes: see events.js for deails-->
@endsection
