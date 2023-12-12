@extends('admin/layouts/layoutMaster')

@section('title', 'Edit - Invoice')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<style>
  .expanded-row{
    background-color: rgb(202, 202, 209) !important;
  }
</style>
@livewireStyles
<x-comments::styles />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/custom/flatpickr.js')}}"></script>
<script src="{{asset('assets/js/custom/select2.js')}}"></script>
<script src={{asset('assets/js/custom/admin-contract-phase-create.js')}}></script>
<script>
  window.active_contract = {{$invoice->contract->id}};
  function initSortable() {
    var sortable = Sortable.create(document.getElementById('billing-items-container'), {
      filter: '.filtered',
      handle: '.bi-drag',
      group: 'shared',
      animation: 150,
      dataIdAttr: 'data-id',
      onSort: function (/**Event*/evt) {
        $.ajax({
          url: route('admin.invoices.invoice-items.sort', { invoice: {{$invoice->id}}}),
          type: "PUT",
          data: {
            items: sortable.toArray().filter(function (el) {
              return el != 'exclude-sort';
            }),
          },
          success: function(res){
          }
        });
      },

    });
  }
  function initPhasesDataTable(){
    $('#phases-invoice-item-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: route('admin.invoices.invoice-items.create', {'invoice' : {{$invoice->id}}, 'type': 'jsonData'}), // Replace with your route for fetching data
        columns: [
            { targets: 0, data: 'id',
              render: function (data, type, row, meta) {
                return '<input type="checkbox" class="form-check-input phase-item" name="phases[]" value="'+data+'">';
              },
              orderable: false, searchable: false},
            { data: 'name', name: 'name' },
            { data: 'stage.name', name: 'stage.name' },
            { data: 'total_cost', name: 'total_cost' },
            { data: 'status', name: 'status', searchable: false },
        ]
    });
  }
  function reloadPhasesList(tab){
    tab = tab ? tab : (window.invoice_active_tab ? window.invoice_active_tab : 'summary');
    reloadItemEditModal();
    $('#nav-' + tab).html('<div class="text-center my-5"><div class="spinner-border text-primary mt-5" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    window.invoice_active_tab = tab;
    $.ajax({
      url: route('admin.invoices.invoice-items.index', { invoice: {{$invoice->id}}, mode: 'edit', tab: tab }),
      type: "GET",
      success: function(data) {
        $('#nav-' + window.invoice_active_tab).html(data.data.view_data);
        // $('#billing-items-container-header').siblings().remove();
        // $('#billing-items-container-header').after(data.data.view_data);
        // $('.invoice-calculations').html(data.data.summary);
        // $('#balance-summary').html(data.data.balance_summary);
        initSelect2()
        $('[data-bs-toggle="tooltip"]').tooltip();
      }
    });
  }

  function update_tax_type(){
    const val = $('[name="is_summary_tax_mock"]').val();
    $('input[name="is_summary_tax"]').val(val);
    $('#summary_update_from').submit();
  }

  $(document).on('change', '.invoice_taxes', function(){
    var item_id = $(this).data('item-id');
    var taxes = $(this).val();
    $.ajax({
      url: route('admin.invoices.tax-rates.store', { invoice: {{$invoice->id}}}),
      type: "POST",
      data: {
        item_id: item_id,
        taxes: taxes
      },
      success: function(data) {
        reloadPhasesList();
      }
    });
  })

  $(document).on('change keyup click', '#invoice-rounding-amount', function(){
    const rounding = $(this).is(':checked') ? 1 : 0;

    $.ajax({
      url: route('admin.invoices.update', { invoice: {{$invoice->id}}, type: 'rounding'}),
      type: "PUT",
      data: {
        rounding_amount: rounding
      },
      success: function(data) {
        reloadPhasesList();
      }
    });
  })

  function updateSummaryTax(){
    var taxes = [];
    $('[name="invoice_taxes[]"]:checked').each(function(){
      taxes.push($(this).val());
    })
    $.ajax({
      url: route('admin.invoices.tax-rates.store', { invoice: {{$invoice->id}}}),
      type: "POST",
      data: {
        taxes: taxes
      },
      success: function(data) {
        reloadPhasesList();
        $('#tax-rates').popover('hide');
      }
    });
  }

  $(document).on('click', '.select-items-btn', function(){
    $('[name="selected_phases[]"]').toggleClass('d-none');
    $('.select-all-items').toggleClass('d-none');
  })

  $(document).on('change', '.select-all-items', function(){
    if($(this).is(':checked')){
      $('[name="selected_phases[]"]').prop('checked', true).trigger('change');
    }else{
      $('[name="selected_phases[]"]').prop('checked', false).trigger('change');
    }

  })

  $(document).on('click change', '[name="selected_phases[]"]', function(){
    if($('[name="selected_phases[]"]:checked').length > 0){
      $('.select-items-btn').addClass('d-none');
      $('.delete-items-btn').removeClass('d-none');
    }else{
      $('.delete-items-btn').addClass('d-none');
      $('.select-items-btn').removeClass('d-none');
    }

    // if all items are selected then check select all checkbox
    if($('[name="selected_phases[]"]').length == $('[name="selected_phases[]"]:checked').length){
      $('.select-all-items').prop('checked', true);
    }else{
      $('.select-all-items').prop('checked', false);
    }
  })

  $(document).on('click', '.delete-items-btn', function(){
    var ids = [];
    $('[name="selected_phases[]"]:checked').each(function(){
      ids.push($(this).val());
    })
    $.ajax({
      url: route('admin.invoices.invoice-items.destroy', { invoice: {{$invoice->id}} , invoice_item: ids}),
      type: "DELETE",
      data: {
        ids: ids
      },
      success: function(data) {
        $('.delete-items-btn').addClass('d-none');
        $('.select-items-btn').removeClass('d-none');
        reloadPhasesList();
      }
    });
  })

  $(document).on('change', '[name="downpayment_type"]', function(){
    if($(this).val() != 'Fixed'){
      $('[name="downpayment_amount"]').parent().removeClass('d-none');
    }else{
      $('[name="downpayment_amount"]').parent().addClass('d-none');
    }
  })

  $(document).on('keyup', '[name="downpayment_value"]', function(){
    console.log($('[name="downpayment_type"]').val());
    if($('[name="downpayment_type"]').val() == 'Percentage'){
      // get data-amount attr from selected option
      var subtotal = $('[name="downpayment_id"]').find(':selected').data('amount');
      var value = $(this).val();
      var amount = parseFloat(subtotal) * parseFloat(value) / 100;
      $('[name="downpayment_amount"]').val(amount);
    }else if($('[name="downpayment_type"]').val() == 'InvPerc'){
      // get data-amount attr from selected option
      var subtotal = $('.invoice_total').data('amount');
      var value = $(this).val();
      var amount = parseFloat(subtotal) * parseFloat(value) / 100;
      $('[name="downpayment_amount"]').val(amount);
    }
  })

  $(document).ready(function() {
    initDropzone();
    initSortable();
      var options = {
          html: true,
          content: $('[data-name="popover-tax-rates"]'),
          placement: 'top'
      }
      var exampleEl = document.getElementById('tax-rates')
      if(exampleEl)
      var popover = new bootstrap.Popover(exampleEl, options)

      // discount popover
      var options = {
          html: true,
          content: $('[data-name="popover-invoice-discount"]'),
          placement: 'top'
      }
      var elm = document.getElementById('invoice-discount')
      var popover = new bootstrap.Popover(elm, options)

      // adjustment popover
      var options = {
          html: true,
          content: $('[data-name="popover-invoice-adjustment"]'),
          placement: 'top'
      }
      var elm = document.getElementById('invoice-adjustment')
      var popover = new bootstrap.Popover(elm, options)

      // retention popover
      var options = {
          html: true,
          content: $('[data-name="popover-invoice-retention"]'),
          placement: 'top'
      }
      var elm = document.getElementById('invoice-retention')
      var popover = new bootstrap.Popover(elm, options)

      // downpayment popover
      var options = {
          html: true,
          content: $('[data-name="popover-invoice-downpayment"]'),
          placement: 'top'
      }
      var elm = document.getElementById('invoice-downpayment')
      var popover = new bootstrap.Popover(elm, options)
  })

  function initDropzone()
  {
    // previewTemplate: Updated Dropzone default previewTemplate
    // ! Don't change it unless you really know what you are doing
    const previewTemplate = `<div class="dz-preview dz-file-preview">
        <div class="dz-details">
          <div class="dz-thumbnail">
            <img data-dz-thumbnail>
            <span class="dz-nopreview">No preview</span>
            <div class="dz-success-mark"></div>
            <div class="dz-error-mark"></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="progress">
              <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
            </div>
          </div>
          <div class="dz-filename" data-dz-name></div>
          <div class="dz-size" data-dz-size></div>
        </div>
      </div>`;

    $('.dropzone').each(function(){
      var $this = this;
      const dropzone = new Dropzone($this, {
        // const dropzoneMulti = new Dropzone('#dropzone-multi', {
        previewTemplate: previewTemplate,
        parallelUploads: 4,
        maxFiles: 20,
        addRemoveLinks: true,
        chunking: true,
        method: "POST",
        maxFilesize: 100,
        chunkSize: 1900000,
        autoProcessQueue : true,
        // If true, the individual chunks of a file are being uploaded simultaneously.
        parallelChunkUploads: true,
        retryChunks: true,
        acceptedFiles: 'text/plain,application/*,image/*,video/*,audio/*',
        url: $($this).data('upload-url'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(file, response) {
            $('#file-upload').before(`<span id="attachment_button_${response.data.id}" class="btn btn-outline-primary mb-1 me-1 btn-sm"><a href="${response.data.url}">${response.data.name}</a>
                <span data-toggle="ajax-delete" data-href="${response.data.del_url}"><i class="fa-regular fa-circle-xmark ps-2"></i></span>
              </span>`);
            this.removeFile(file);
            if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
              $('.dropzone').addClass('d-none');
            }
        },
        init: function(){
            this.on("maxfilesexceeded", function(file){
                alert("No more files please!");
            });
            this.on("error", function(file, errorMessage, xhr){
              // Check if the response is a validation error
              if (xhr.status === 422) {
                // Parse the validation errors from the response
                var errors = JSON.parse(xhr.responseText).errors;

                // Loop through the validation errors and add them to the file preview
                $.each(errors, function(key, value) {
                  var error = value[0];
                  var dzError = $('<div>').addClass('dz-error-message').text(error);
                  $(file.previewElement).append(dzError);
                });
              }
            })
        }
      });
    });
  }
  function removeMedia(id){
    $('#attachment_button_'+id).remove();
  }
</script>

<script src="{{asset('assets/js/custom/admin-invoice-item-update.js')}}"></script>
@endsection

@section('content')
@includeWhen(isset($invoice) ,'admin.pages.invoices.header-top', ['tab' => 'invoice'])
{{-- Alert Contract expired --}}
@if(count($pendingDocs) > 0)
  <div class="col-12 mb-4">
    <div role="alert" class="alert alert-danger alert-dismissible">
      <h5 class="alert-heading mb-2"> <span class="alert-icon text-danger me-2">
        <i class="ti ti-bell ti-xs"></i>
      </span> {{__('Contract has pending documents')}}</h5>
      <p class="mb-2"><strong>The following documents are missing from the contract.</strong></p>
      <ul>
      @forelse ($pendingDocs as $pd)
        <li class="mb-1">{{$pd->title}}</>
      @empty
        </ul>
      @endforelse
      <p class="mb-0 d-flex justify-content-end"><a class="btn btn-outline-danger" href="{{route('admin.contracts.pending-documents.index', [$invoice->contract])}}">Please upload here</a></p>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
@endif
@forelse ($invoice->deductableDownpayments as $dp)
@if ($dp->downpayment_amount_remaining > 0)
  <div class="col-12 mb-4">
    <div role="alert" class="alert alert-warning alert-dismissible">
      <h5 class="alert-heading mb-2"> <span class="alert-icon text-warning me-2">
        <i class="ti ti-bell ti-xs"></i>
      </span> {{__('Down payment Invoice Available')}}</h5>
      <p class="mb-2"><strong>You have down payment invoice with unsetteled balance: @cMoney($dp->downpayment_amount_remaining, $invoice->contract->currency, true)</strong></p>
      <p class="mb-0 d-flex justify-content-end"><a class="btn btn-outline-warning" href="{{route('admin.invoices.edit', [$dp->id])}}">View</a></p>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
@endif
@empty
@endforelse
<form action="{{route('admin.invoices.update', [$invoice])}}" method="POST">
  @method('PUT')
  @csrf
<div class="row invoice-edit">
  <!-- Invoice Edit-->
  <div class="col-lg-9 col-12 mb-lg-0 mb-4">
    <div class="card invoice-preview-card">
      <div class="card-body">
        <div>
          <dt class="col-sm-6 mb-2 mb-sm-0 text-md-start ps-sm-2">
            <span class="h4 text-capitalize mb-0 text-nowrap">Invoice</span>
          </dt>
          <dd class="col-sm-6 d-flex justify-content-md-start pe-0 ps-sm-2">
              <span>{{runtimeInvIdFormat($invoice->id)}}</span>
          </dd>
        </div>

        <hr class="my-3 mx-n4" />

        <div class="row m-sm-4 m-0">
          <div class="col-md-6 mb-md-0 mb-4 ps-0">
            <div class="d-flex svg-illustration mb-4 gap-2 align-items-center">
              <div style="background-color: black;">
                @include('_partials.macros',["height"=>35,"withbg"=>''])
              </div>

              <span class="app-brand-text fw-bold fs-4">
                {{ config('variables.templateName') }}
              </span>
            </div>
            <p class="mb-2">Office 149, 450 South Brand Brooklyn</p>
            <p class="mb-2">San Diego County, CA 91905, USA</p>
            <p class="mb-3">+1 (123) 456 7891, +44 (876) 543 2198</p>
          </div>
          <div class="col-md-6 col-sm-5 col-12 mb-sm-0 mb-4">
            <dl class="row mb-2 text-end">
              <h6 class="mb-4">Invoice To:</h6>
              <p class="mb-1">{{$invoice->company->name}}</p>
              <p class="mb-1">{{$invoice->company->address}}</p>
              <p class="mb-0">{{$invoice->company->email}}</p>
            </dl>
          </div>
        </div>

        <hr class="my-3 mx-n4" />

        <div class="p-sm-4 p-0 d-flex justify-content-between">
          <div>
            <table>
              <tbody>
                <tr>
                  <td>Invoice Date</td>
                  <td>
                    {!! Form::date('invoice_date', $invoice->invoice_date, ['class' => 'form-control flatpickr', 'placeholder' => 'YYYY-MM-DD', 'disabled' => !$is_editable]) !!}
                  </td>
                </tr>
                <tr>
                  <td>Due Date </td>
                  <td>
                    {!! Form::date('due_date', $invoice->due_date, ['class' => 'form-control flatpickr', 'placeholder' => 'YYYY-MM-DD', 'disabled' => !$is_editable]) !!}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div>
            <table>
                <tbody id="balance-summary">
                  @include('admin.pages.invoices.balance-summary')
                </tbody>
            </table>
          </div>
        </div>

        <hr class="my-0" />

        @if ($invoice->type == 'Partial Invoice')
          <div class="source-item pt-4 px-0 px-sm-4">
            <div class="col-12">
              <div class="table-responsive m-t-40">
                <table class="table table-hover">
                  <thead>
                    <tr>
                        <th class="text-left x-description bill_col_description">Phase</th>
                        <th class="text-left x-description bill_col_description">Total Cost</th>
                        <th class="text-left x-rate bill_col_rate">Invoiceable Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $phase = $invoice->items->where('invoiceable_type', 'App\Models\ContractPhase')[0];
                    @endphp
                    <tr>
                      <td class="">{{$phase->invoiceable->name ?? runtimeInvIdFormat($phase->invoiceable_id)}}</td>
                      <!--total-->
                      <td class="text-right">
                          @cMoney($phase->invoiceable->total_cost, $invoice->contract->currency, true)
                      </td>
                      <!--total-->
                      <!-- invoiceable amount -->
                      <td class="text-right">
                        @cMoney($phase->invoiceable->getRemainingAmount() + $invoice->total, $invoice->contract->currency, true)
                      </td>
                      <!-- invoiceable amount -->
                    </tr>
                    {{-- @include('admin.pages.invoices.items.edit-list') --}}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @endif

        <hr class="my-3 mx-n4" />
        <div class="row mb-4">
          <div class="nav-align-top">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item" onclick="reloadPhasesList('summary')">
                <button type="button" class="nav-link {{$tab == 'summary' ? 'show active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#nav-summary" aria-selected="true">Summary</button>
              </li>
                <li class="nav-item" onclick="reloadPhasesList('tax-report')">
                  <button type="button" class="nav-link {{$tab == 'tax-report' ? 'show active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#nav-tax-report" aria-selected="false">Tax Report</button>
                </li>
                <li class="nav-item" onclick="reloadPhasesList('authority-tax')">
                  <button type="button" class="nav-link {{$tab == 'authority-tax' ? 'show active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#nav-authority-tax" aria-selected="false">Tax Authority</button>
                </li>
            </ul>
            <div class="tab-content p-0">
              <div class="tab-pane {{$tab == 'summary' ? 'show active' : 'fade'}}" id="nav-summary" role="tabpanel">
                @includeWhen(!isset($tab) || $tab == 'summary', 'admin.pages.invoices.tabs.summary', ['tab' => 'summary'])
              </div>
              <div class="tab-pane {{$tab == 'tax-report' ? 'show active' : 'fade'}}" id="nav-tax-report" role="tabpanel">
                @includeWhen(isset($tab) && $tab == 'tax-report','admin.pages.invoices.tabs.summary', ['tab' => 'tax-report'])
              </div>
              <div class="tab-pane {{$tab == 'authority-tax' ? 'show active' : 'fade'}}" id="nav-authority-tax" role="tabpanel">
                @includeWhen(isset($tab) && $tab == 'authority-tax', 'admin.pages.invoices.tabs.summary', ['tab' => 'authority-tax'])
              </div>
            </div>
          </div>
        </div>

        @if($is_editable)
          <div class="row p-sm-2 pe-4">
            <div class="col-12 d-flex justify-content-end">
              <section class="center">
                <button id="invoice-downpayment" type="button" tabindex="0" class="btn btn-sm me-1 btn-outline-{{count($invoice->deductableDownpayments) == 0 ? 'muted disabled' : 'primary'}} rounded-pill" data-bs-toggle="popover">Down payment</button>
              </section>
              <section class="center">
                <button id="invoice-retention" type="button" tabindex="0" class="btn btn-sm me-1 btn-outline-primary rounded-pill" data-bs-toggle="popover">Retention</button>
              </section>
              <section class="center">
                <button id="invoice-adjustment" type="button" tabindex="0" class="btn btn-sm me-1 btn-outline-primary rounded-pill" data-bs-toggle="popover">Adjustment</button>
              </section>
              <section class="center">
                <button id="invoice-discount" type="button" tabindex="0" class="btn btn-sm me-1 btn-outline-primary rounded-pill" data-bs-toggle="popover">Discount</button>
              </section>
              @if ($invoice->is_summary_tax)
                <section class="center">
                  <div hidden>
                      <div data-name="popover-tax-rates">
                        <div class="d-flex justify-content-between">
                          <b>Tax Rates</b>
                          <button type="button" class="btn-close" onclick="$('#tax-rates').popover('hide');" aria-label="Close"></button>
                        </div>
                        <hr class="m-0">
                        <div class="mt-2">
                          @forelse ($tax_rates->where('is_retention', false) as $tax)
                          <div class="form-check">
                            <input class="form-check-input" name="invoice_taxes[]" type="checkbox" value="{{$tax->id}}" id="tax-{{$tax->id}}">
                            <label class="form-check-label" for="tax-{{$tax->id}}">
                              {{$tax->name}} (
                                @if($tax->type != 'Percent')
                                  @cMoney($tax->amount, $invoice->contract->currency, true)
                                @else
                                  {{$tax->amount}}%
                                @endif
                              )
                            </label>
                          </div>
                          @empty
                          @endforelse
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                          <button class="btn btn-primary btn-sm" onclick="updateSummaryTax()">Update</button>
                        </div>
                      </div>
                  </div>
                  <button id="tax-rates" type="button" tabindex="0" class="btn btn-sm me-1 btn-outline-primary rounded-pill" data-bs-toggle="popover">Tax Rates</button>
                </section>
              @endif
              <div class="dropdown dropup {{$invoice->type == 'Regular' ? 'd-none' : ''}}">
                <button class="btn btn-outline-primary dropdown-toggle rounded-pill btn-sm" type="button" id="tax-type" data-bs-toggle="dropdown" aria-expanded="false">
                  Tax Type
                </button>
                <div>
                  <div class="dropdown-menu p-2" aria-labelledby="tax-type">
                    <div class="d-flex justify-content-between">
                      <b>Tax Type</b>
                    </div>
                    <hr class="m-0">
                    <div class="my-3 m-2">
                      <select class="form-select form-select-sm" name="is_summary_tax_mock">
                        <option @selected($invoice->is_summary_tax) value="1">Summary</option>
                        <option @selected(!$invoice->is_summary_tax) value="0">Inline</option>
                      </select>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                      <button class="btn btn-primary btn-sm" type="button" onclick="update_tax_type()">Update</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif

        <hr class="my-3 mx-n4" />
        {{-- <div class="row px-0 px-sm-4">
          <div class="col-12">
          </div>
        </div> --}}

        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="col-12 py-3">
              <p>{{ __('Attachments :') }}</p>
              @forelse ($invoice->media as $media)
              <span id="attachment_button_{{$media->id}}" class="btn btn-outline-primary mb-1 btn-sm"><a href="{{$media->getDownloadUrl()}}">{{substr($media->filename, 0, 10)}}</a>
                <span data-toggle="ajax-delete" data-href="{{route('admin.invoices.attachments.destroy', [$invoice, $media->id])}}"><i class="fa-regular fa-circle-xmark ps-2"></i></span>
              </span>
              @empty
              @endforelse
              @if($is_editable)
                <span id="file-upload" onclick="$('.dropzone').toggleClass('d-none');" class="btn btn-primary mb-1 btn-sm"><i class="fa-solid fa-circle-plus me-1"></i> {{__('Add Attachments')}}
                </span>
                <div class="dropzone d-none needsclick" data-upload-url="{{ route('admin.invoices.attachments.store', [$invoice])}}">
                  <div class="dz-message needsclick">
                    <small class="h6">Drop the file here or click to upload </small>
                  </div>
                  <div class="fallback">
                    <input name="file" type="file" />
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="mb-3">
              <label for="refrence_id" class="form-label fw-semibold">{{__('Refrence ID')}}</label>
              <input type="text" name="refrence_id" {{$is_editable ?: 'disabled'}} id="refrence_id" value="{{$invoice->refrence_id}}" class="form-control" placeholder="{{__('Refrence ID')}}">
            </div>
          </div>
        </div>

        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="mb-3">
              <label for="terms" class="form-label fw-semibold">Terms:</label>
              <textarea name="terms" class="form-control" {{$is_editable ?: 'disabled'}} rows="2" id="terms">{{$invoice->terms}}</textarea>
            </div>
          </div>
        </div>
        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="mb-3">
              <label for="note" class="form-label fw-semibold">Note:</label>
              <textarea name="note" class="form-control" {{$is_editable ?: 'disabled'}} rows="2" id="note">{{$invoice->note}}</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Invoice Edit-->

  <!-- Invoice Actions -->
  <div class="col-lg-3 col-12 invoice-actions">
    @if($invoice->status != 'Void')
      <div class="card mb-4">
        <div class="card-body">
          <button class="btn btn-primary d-grid w-100" type="button" data-form="ajax-form">
            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-1"></i>Save Invoice</span>
          </button>
          <button class="btn btn-primary d-grid w-100 mt-2" type="button" data-toggle="ajax-modal" data-title="{{__('Merge Invoices')}}" data-href="{{route('admin.invoices.merge-invoices.create', [$invoice])}}">
            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-1"></i>Merge Invoice</span>
          </button>
          <button class="btn btn-primary d-grid mt-2 w-100" type="button" data-toggle="ajax-modal" data-title="{{__('Add Payment')}}" data-href="{{route('admin.finances.payments.create',['invoice' => $invoice->id])}}">
            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-currency-dollar ti-xs me-1"></i>Add Payment</span>
          </button>
          @if(!in_array($invoice->status, ['Paid', 'Partial Paid']))
            <button class="btn btn-primary d-grid mt-2 w-100" type="button" data-toggle="ajax-modal" data-title="{{__('Make Void')}}" data-href="{{route('admin.invoices.status.create',['invoice' => $invoice->id])}}">
              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-currency-dollar ti-xs me-1"></i>Make Void</span>
            </button>
          @endif
        </div>
      </div>
    @endif
  </div>
</form>
  <form id="summary_update_from" action="{{route('admin.invoices.update', [$invoice, 'update_tax_type' => 1])}}" method="POST">
    @method('PUT')
    @csrf
    {!! Form::hidden('is_summary_tax', 1,) !!}
  </form>
  {{-- discount Popover --}}
  <div hidden>
    <div data-name="popover-invoice-discount">
      <form method="POST" action="{{route('admin.invoices.update', [$invoice, 'update_discount' => 1])}}">
        @method('PUT')
        <div class="d-flex justify-content-between">
          <b>Discount</b>
          <button type="button" class="btn-close" onclick="$('#invoice-discount').popover('hide');" aria-label="Close"></button>
        </div>
        <hr class="m-0">
        <div>
          <div class="form-group">
            {{ Form::label('discount_type', __('Discount Value Type'), ['class' => 'col-form-label']) }}
            {!! Form::select('discount_type', ['0' => 'Select Type', 'Fixed' => 'Fixed', 'Percentage' => 'Percentage'], null, ['class' => 'form-select']) !!}
          </div>
          <div class="form-group">
            {{ Form::label('discount_value', __('Discount Value'), ['class' => 'col-form-label']) }}
            {!! Form::number('discount_value', null, ['class' => 'form-control', 'placeholder' => __('0.00')]) !!}
          </div>
        </div>
        <div class="d-flex justify-content-end mt-2">
          <button class="btn btn-primary btn-sm" data-form="ajax-form">Update</button>
        </div>
      </form>
    </div>
  </div>
  {{-- adjustment Popover --}}
  <div hidden>
    <div data-name="popover-invoice-adjustment">
      <form method="POST" action="{{route('admin.invoices.update', [$invoice, 'update_adjustment' => 1])}}">
        @method('PUT')
        <div class="d-flex justify-content-between">
          <b>Adjustment</b>
          <button type="button" class="btn-close" onclick="$('#invoice-adjustment').popover('hide');" aria-label="Close"></button>
        </div>
        <hr class="m-0">
        <div>
          <div class="form-group">
            {{ Form::label('adjustment_description', __('Description'), ['class' => 'col-form-label']) }}
            {!! Form::text('adjustment_description', null, ['class' => 'form-control']) !!}
          </div>
          <div class="form-group">
            {{ Form::label('adjustment_amount', __('Adjustment Amount'), ['class' => 'col-form-label']) }}
            {!! Form::number('adjustment_amount', null, ['class' => 'form-control', 'placeholder' => __('0.00')]) !!}
          </div>
        </div>
        <div class="d-flex justify-content-end mt-2">
          <button class="btn btn-primary btn-sm" data-form="ajax-form">Update</button>
        </div>
      </form>
    </div>
  </div>
  {{-- retention Popover --}}
  <div hidden>
    <div data-name="popover-invoice-retention">
      <form method="POST" action="{{route('admin.invoices.update', [$invoice, 'update_retention' => 1])}}">
        @method('PUT')
        <div class="d-flex justify-content-between">
          <b>Retention</b>
          <button type="button" class="btn-close" onclick="$('#invoice-retention').popover('hide');" aria-label="Close"></button>
        </div>
        <hr class="m-0">
        <div>
          <div class="form-group">
            {{ Form::label('retention_id', __(' Retention'), ['class' => 'col-form-label']) }}
            <select name="retention_id" id="retention_id" class="form-select select2">
              <option value="">{{__('Select Retention')}}</option>
              @forelse ($tax_rates->where('config_type', 'Retention') as $ret)
                <option value="{{$ret->id}}">{{$ret->name}} (
                  @if ($ret->type != 'Percent')
                      @cMoney($ret->amount, $invoice->contract->currency, true)
                  @else
                      {{$ret->amount}}%
                  @endif
                )</option>
              @empty
              @endforelse
            </select>
          </div>
          {{-- <div class="form-group">
            {{ Form::label('retention_value', __('Retention Value'), ['class' => 'col-form-label']) }}
            {!! Form::number('retention_value', null, ['class' => 'form-control', 'placeholder' => __('0.00')]) !!}
          </div> --}}
        </div>
        <div class="d-flex justify-content-end mt-2">
          <button class="btn btn-primary btn-sm" data-form="ajax-form">Update</button>
        </div>
      </form>
    </div>
  </div>
  {{-- end retention popover --}}
  {{-- downpayment Popover --}}
  <div hidden>
    <div data-name="popover-invoice-downpayment">
      <form method="POST" action="{{route('admin.invoices.downpayments.store', [$invoice])}}">
        <div class="d-flex justify-content-between">
          <b>Down payment</b>
          <button type="button" class="btn-close" onclick="$('#invoice-downpayment').popover('hide');" aria-label="Close"></button>
        </div>
        <hr class="m-0">
        <div>
          <div class="form-group">
            {{ Form::label('downpayment_id', __(' Downpayment'), ['class' => 'col-form-label']) }}
            <select name="downpayment_id" id="downpayment_id" class="form-select select2">
              <option value="">{{__('Select Down payment')}}</option>
              @forelse ($invoice->deductableDownpayments as $dp)
                <option data-amount="{{$dp->total}}" value="{{$dp->id}}">{{runtimeInvIdFormat($dp->id)}} ( Total: @cMoney($dp->total, $invoice->contract->currency, true) )</option>
              @empty
              @endforelse
            </select>
          </div>
          <div class="form-group">
            {{ Form::label('downpayment_type', __('Amount Type'), ['class' => 'col-form-label']) }}
            {!! Form::select('downpayment_type', ['Fixed' => 'Fixed', 'Percentage' => 'Percentage', 'InvPerc' => 'Percentage of Invoice'], null, ['class' => 'form-select select2']) !!}
          </div>
          <div class="form-group">
            {{ Form::label('downpayment_value', __('Retention Value'), ['class' => 'col-form-label']) }}
            {!! Form::number('downpayment_value', null, ['class' => 'form-control', 'placeholder' => __('0.00')]) !!}
          </div>
          <div class="form-group d-none">
            {{ Form::label('downpayment_amount', __('Actual Amount'), ['class' => 'col-form-label']) }}
            {!! Form::number('downpayment_amount', null, ['class' => 'form-control', 'placeholder' => __('0.00'), 'disabled']) !!}
          </div>
        </div>
        <div class="d-flex justify-content-end mt-2">
          <button class="btn btn-primary btn-sm" data-form="ajax-form">Update</button>
        </div>
      </form>
    </div>
  </div>
  {{-- end downpayment popover --}}
</div>
@endsection
<script>
  window.oURL = window.location.href;

  function liveWireRescan()
  {
    Livewire.rescan(document.getElementById('comments-section'));
    Alpine.initTree(document.getElementById('comments-section'));
    setTimeout(function () {
          history.replaceState(null, null, oURL);
    }, 1000);
  }
</script>
@livewireScripts
<x-comments::scripts />
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
