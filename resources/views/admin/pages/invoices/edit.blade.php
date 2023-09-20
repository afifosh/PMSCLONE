@extends('admin/layouts/layoutMaster')

@section('title', 'Edit - Invoice')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/offcanvas-add-payment.js')}}"></script>
<script src="{{asset('assets/js/offcanvas-send-invoice.js')}}"></script>
<script src="{{asset('assets/js/app-invoice-edit.js')}}"></script>
<script src="{{asset('assets/js/custom/flatpickr.js')}}"></script>
<script>
  function reloadMilestonesList(){
    $.ajax({
      url: route('admin.invoices.invoice-items.index', { invoice: {{$invoice->id}}, mode: 'edit' }),
      type: "GET",
      success: function(data) {
        $('#billing-items-container').html(data.data.view_data);
      }
    });
  }
</script>
@endsection

@section('content')
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

        <form class="source-item pt-4 px-0 px-sm-4">
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
                            <th class="text-left x-tax bill_col_tax ">
                                Tax</th>
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
        </form>

        <hr class="my-3 mx-n4" />

        <div class="row p-0 p-sm-4">
          <div class="col-md-6 mb-md-0 mb-3">
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            <div class="invoice-calculations">
              <div class="d-flex justify-content-between mb-2">
                <span class="w-px-100">Subtotal:</span>
                <span class="fw-semibold">$5000.25</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="w-px-100">Discount:</span>
                <span class="fw-semibold">$00.00</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="w-px-100">Tax:</span>
                <span class="fw-semibold">$100.00</span>
              </div>
              <hr />
              <div class="d-flex justify-content-between">
                <span class="w-px-100">Total:</span>
                <span class="fw-semibold">$5100.25</span>
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
        <button class="btn btn-primary d-grid w-100" data-bs-toggle="offcanvas" data-bs-target="#sendInvoiceOffcanvas">
          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-1"></i>Send Invoice</span>
        </button>
        <div class="d-flex my-2">
          <a href="{{url('app/invoice/preview')}}" class="btn btn-label-secondary w-100 me-2">Preview</a>
          <button type="button" class="btn btn-label-secondary w-100">Save</button>
        </div>
        <button class="btn btn-primary d-grid w-100" data-bs-toggle="offcanvas" data-bs-target="#addPaymentOffcanvas">
          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-currency-dollar ti-xs me-1"></i>Add Payment</span>
        </button>
      </div>
    </div>
  </div>
  <!-- /Invoice Actions -->
</div>

<!-- Offcanvas -->
@include('_partials/_offcanvas/offcanvas-send-invoice')
@include('_partials/_offcanvas/offcanvas-add-payment')
<!-- /Offcanvas -->
@endsection
