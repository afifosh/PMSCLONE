@extends('admin.layouts/layoutMaster')
@section('page-style')
<link href="{{asset('assets/css/invoices/bootstrap.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/css/invoices/vendor.css')}}">


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

<script type="text/javascript">
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
<div class="container-fluid {{ $page['mode'] ?? '' }}" id="invoice-container">

    <!--HEADER SECTION-->
    <div class="row page-titles">

        <!--BREAD CRUMBS & TITLE-->
        <div class="col-md-12 col-lg-7 align-self-center {{ $page['crumbs_special_class'] ?? '' }}" id="breadcrumbs">
            <!--attached to project-->
            <a id="InvoiceTitleAttached"
                class="{{ runtimeInvoiceAttachedProject('project-title', $bill->bill_projectid) }}"
                href="{{ _url('projects/'.$bill->bill_projectid) }}">
                <h3 class="text-themecolor" id="InvoiceTitleProject">{{ $page['heading'] ?? '' }}</h3>
            </a>
            <!--not attached to project-->
            <h4 id="InvoiceTitleNotAttached"
                class="muted {{ runtimeInvoiceAttachedProject('alternative-title', $bill->bill_projectid) }}">{{ cleanLang(__('lang.not_attached_to_project')) }}</h4>
            <!--crumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{ cleanLang(__('lang.app')) }}</li>
                @if(isset($page['crumbs']))
                @foreach ($page['crumbs'] as $title)
                <li class="breadcrumb-item @if ($loop->last) active active-bread-crumb @endif">{{ $title ?? '' }}</li>
                @endforeach
                @endif
            </ol>
            <!--crumbs-->
        </div>

        <!--ACTIONS-->
        @if($bill->bill_type == 'invoice')
        @include('pages.bill.components.misc.invoice.actions')
        @endif
        @if($bill->bill_type == 'estimate')
        @include('pages.bill.components.misc.estimate.actions')
        @endif

    </div>
    <!--/#HEADER SECTION-->

    <!--BILL CONTENT-->
    <div class="row">
        <div class="col-md-12 p-t-30">
            @include('pages.bill.bill-web')
        </div>
    </div>
</div>
<!--main content -->

@endsection
