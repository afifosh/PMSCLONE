@extends('admin/layouts/layoutMaster')

@section('title', 'Edit - Invoice')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/offcanvas-add-payment.js')}}"></script>
<script src="{{asset('assets/js/offcanvas-send-invoice.js')}}"></script>
<script src="{{asset('assets/js/app-invoice-edit.js')}}"></script>
<script src="{{asset('assets/js/custom/flatpickr.js')}}"></script>
<script src="{{asset('assets/js/custom/select2.js')}}"></script>
<script>
  function reloadMilestonesList(){
    $.ajax({
      url: route('admin.invoices.invoice-items.index', { invoice: {{$invoice->id}}, mode: 'edit' }),
      type: "GET",
      success: function(data) {
        $('#billing-items-container').html(data.data.view_data);
        $('.invoice-calculations').html(data.data.summary);
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
        reloadMilestonesList();
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
        reloadMilestonesList();
        $('#tax-rates').popover('hide');
      }
    });
  }

  $(document).ready(function() {
      var options = {
          html: true,
          content: $('[data-name="popover-tax-rates"]'),
          placement: 'top'
      }
      var exampleEl = document.getElementById('tax-rates')
      var popover = new bootstrap.Popover(exampleEl, options)
  })
</script>
@endsection

@section('content')
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

        <div class="row p-sm-4 p-0">
          <div class="col-">
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
        </div>

        <hr class="my-3 mx-n4" />

        <div class="source-item pt-4 px-0 px-sm-4">
          <div class="col-12">
            <div class="table-responsive m-t-40 invoice-table-wrapper editing clear-both">
                <table class="table table-hover invoice-table editing">
                    <thead>
                        <tr>
                            <!--action-->
                            <th class="text-left x-action bill_col_action"></th>
                            <!--description-->
                            <th class="text-left x-description bill_col_description">Item
                            </th>
                            <th class="text-left x-rate bill_col_rate">Amount</th>
                            <!--tax-->
                            @if (!$invoice->is_summary_tax)
                              <th class="text-left x-tax bill_col_tax ">Tax</th>
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
              <button type="button" class="btn btn-primary" data-title="{{__('Add Milestones')}}" data-toggle='ajax-modal' data-href="{{route('admin.invoices.invoice-items.create',[$invoice])}}">Add Milestones</button>
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
                        @forelse ($tax_rates as $tax)
                        <div class="form-check">
                          <input class="form-check-input" name="invoice_taxes[]" type="checkbox" value="{{$tax->id}}" id="tax-{{$tax->id}}">
                          <label class="form-check-label" for="tax-{{$tax->id}}">
                            {{$tax->name}} ({{$tax->amount}}{{$tax->type == 'Percent' ? '%' : ''}})
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
                <button id="tax-rates" type="button" tabindex="0" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="popover">Tax Rates</button>
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

        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="mb-3">
              <label for="terms" class="form-label fw-semibold">Terms:</label>
              <textarea class="form-control" rows="2" id="terms">{{$invoice->terms}}</textarea>
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
  <!-- /Invoice Actions -->
</div>

<!-- Offcanvas -->
@include('_partials/_offcanvas/offcanvas-send-invoice')
@include('_partials/_offcanvas/offcanvas-add-payment')
<!-- /Offcanvas -->
@endsection
