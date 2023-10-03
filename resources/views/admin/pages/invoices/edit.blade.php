@extends('admin/layouts/layoutMaster')

@section('title', 'Edit - Invoice')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/custom/flatpickr.js')}}"></script>
<script src="{{asset('assets/js/custom/select2.js')}}"></script>
<script>
  function initSortable() {
    var sortable = Sortable.create(document.getElementById('billing-items-container'), {
      handle: '.bi-drag',
      group: 'shared',
      animation: 150,
      dataIdAttr: 'data-id',
      onSort: function (/**Event*/evt) {
        $.ajax({
          url: route('admin.invoices.invoice-items.sort', { invoice: {{$invoice->id}}}),
          type: "PUT",
          data: {
            items: sortable.toArray(),
          },
          success: function(res){
          }
        });
      },

    });
  }
  function reloadPhasesList(){
    $.ajax({
      url: route('admin.invoices.invoice-items.index', { invoice: {{$invoice->id}}, mode: 'edit' }),
      type: "GET",
      success: function(data) {
        $('#billing-items-container').html(data.data.view_data);
        $('.invoice-calculations').html(data.data.summary);
        $('#balance-summary').html(data.data.balance_summary);
        initSelect2()
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
@endsection

@section('content')
{{-- Alert Contract expired --}}
@if(count($pendingDocs) > 0)
  <div class="col-12 mb-4">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <h4 class="alert-heading">{{__('Contract Has Pending Documents')}}</h4>
      <p class="mb-0">The following documents are missing from the contract.</p>
      @forelse ($pendingDocs as $pd)
        <li class="mb-0">{{$pd->title}}</>
      @empty
      @endforelse
      <p class="mb-0 d-flex justify-content-end"><a class="btn btn-outline-danger" href="{{route('admin.contracts.pending-documents.index', [$invoice->contract])}}">Please upload here</a></p>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
@endif
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
                    {!! Form::date('invoice_date', $invoice->invoice_date, ['class' => 'form-control flatpickr', 'placeholder' => 'YYYY-MM-DD']) !!}
                  </td>
                </tr>
                <tr>
                  <td>Due Date </td>
                  <td>
                    {!! Form::date('due_date', $invoice->due_date, ['class' => 'form-control flatpickr', 'placeholder' => 'YYYY-MM-DD']) !!}
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

        <hr class="my-3 mx-n4" />

        <div class="source-item pt-4 px-0 px-sm-4">
          <div class="col-12">
            <div class="table-responsive m-t-40 invoice-table-wrapper editing clear-both">
                <table class="table table-hover invoice-table editing">
                    <thead>
                        <tr>
                            <!--action-->
                            <th class="text-left x-action bill_col_action">Action</th>
                            <!--description-->
                            <th class="text-left x-description bill_col_description">Item</th>
                            <th class="text-left x-description bill_col_description">Price</th>
                            <th class="text-left x-description bill_col_description">QTY</th>
                            <th class="text-left x-rate bill_col_rate">Subtotal</th>
                            <!--tax-->
                            @if (!$invoice->is_summary_tax)
                              <th class="" style="min-width: 180px;">Tax</th>
                            @endif
                            <!--total-->
                            <th class="text-right x-total bill_col_total" id="bill_col_total">Total
                            </th>
                        </tr>
                    </thead>
                    <tbody id="billing-items-container" class="billing-items-container-editing">
                      @include('admin.pages.invoices.items.edit-list')
                    </tbody>
                </table>
            </div>
        </div>
          <div class="row pb-4">
            <div class="col-12 mt-4">
              <button type="button" class="btn btn-primary" data-title="{{__('Add Item')}}" data-toggle='ajax-modal' data-href="{{route('admin.invoices.custom-invoice-items.create',[$invoice])}}">Add Item</button>
              <button type="button" class="btn btn-primary" data-title="{{__('Add Phases')}}" data-toggle='ajax-modal' data-href="{{route('admin.invoices.invoice-items.create',[$invoice])}}">Add Phases</button>
              <button type="button" class="btn btn-primary" data-title="{{__('Select Retentions')}}" data-toggle='ajax-modal' data-href="{{route('admin.invoices.invoice-items.create',[$invoice, 'type' => 'retentions'])}}">Add Retention</button>
            </div>
          </div>
        </div>

        <hr class="my-3 mx-n4" />

        <div class="row p-0 p-sm-4">
          <div class="col-md-6 mb-md-0 mb-3">
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            <div class="invoice-calculations">
              @include('admin.pages.invoices.items.summary')
            </div>
          </div>
        </div>

        <div class="row p-sm-2 pe-4">
          <div class="col-12 d-flex justify-content-end">
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
                                @money($tax->amount, $invoice->contract->currency, true)
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
            <div class="dropdown dropup">
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
            </div>
          </div>
        </div>

        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="mb-3">
              <label for="refrence_id" class="form-label fw-semibold">{{__('Refrence ID')}}</label>
              <input type="text" name="refrence_id" id="refrence_id" value="{{$invoice->refrence_id}}" class="form-control" placeholder="{{__('Refrence ID')}}">
            </div>
          </div>
        </div>

        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="mb-3">
              <label for="terms" class="form-label fw-semibold">Terms:</label>
              <textarea name="terms" class="form-control" rows="2" id="terms">{{$invoice->terms}}</textarea>
            </div>
          </div>
        </div>
        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="mb-3">
              <label for="note" class="form-label fw-semibold">Note:</label>
              <textarea name="note" class="form-control" rows="2" id="note">{{$invoice->note}}</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Invoice Edit-->

  <!-- Invoice Actions -->
  <div class="col-lg-3 col-12 invoice-actions">
    <div class="card mb-4">
      <div class="card-body">
        <button class="btn btn-primary d-grid w-100" type="button" data-form="ajax-form">
          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-1"></i>Save Invoice</span>
        </button>
        <button class="btn btn-primary d-grid mt-2 w-100" data-bs-toggle="offcanvas" type="button" data-bs-target="#addPaymentOffcanvas">
          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-currency-dollar ti-xs me-1"></i>Add Payment</span>
        </button>
      </div>
    </div>
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
            {{ Form::label('discount_type', __('Discount Type'), ['class' => 'col-form-label']) }}
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
              @forelse ($tax_rates->where('is_retention', true) as $ret)
                <option value="{{$ret->id}}">{{$ret->name}} (
                  @if ($ret->type != 'Percent')
                      @money($ret->amount, $invoice->contract->currency, true)
                  @else
                      {{$ret->amount}}%
                  @endif
                )</option>
              @empty
              @endforelse
            </select>
          </div>
          {{-- <div class="form-group">
            {{ Form::label('retention_type', __('Retention Type'), ['class' => 'col-form-label']) }}
            {!! Form::select('retention_type', ['0' => 'Select Type', 'Fixed' => 'Fixed', 'Percentage' => 'Percentage'], null, ['class' => 'form-select']) !!}
          </div>
          <div class="form-group">
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
</div>
@endsection
