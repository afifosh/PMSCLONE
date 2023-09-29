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
<script src="{{asset('assets/js/custom/flatpickr.js')}}"></script>
<script src="{{asset('assets/js/custom/select2.js')}}"></script>
<script>
  $(document).on('click', '.edit-downpayment', function(){
    $('.downpayment-row').addClass('d-none');
    $('.downpayment-row-edit').removeClass('d-none');
  });
  $(document).on('keyup change', '#dp-subtotal', function(){
    $('#dp-form-subtotal').val($(this).val());
  });
  $(document).on('keyup change', '#dp-description', function(){
    $('#dp-form-description').val($(this).val());
  });
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
                <tbody id="balance-summary" class="d-none">
                  {{-- @include('admin.pages.invoices.balance-summary') --}}
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
                            <th class="text-left x-description bill_col_description">Amount
                            </th>
                            <th class="text-left x-rate bill_col_rate">Description</th>
                            <!--total-->
                            <th class="text-right x-total bill_col_total" id="bill_col_total">Total
                            </th>
                        </tr>
                    </thead>
                    <tbody id="billing-items-container" class="billing-items-container-editing">
                      <tr class="downpayment-row">
                        <td class="text-left x-action edit-downpayment cursor-pointer"><i class="ti ti-edit"></i> </td>
                        <td class="text-left x-rate bill_col_rate">@money($invoice->subtotal, $invoice->contract->currency, true)</td>
                        <td class="text-left x-rate bill_col_rate">{{$invoice->description}}</td>
                        <td class="text-left x-rate bill_col_rate">@money($invoice->subtotal, $invoice->contract->currency, true)</td>
                      </tr>
                      {{-- For editing : convert to input --}}
                      <tr class="d-none downpayment-row-edit">
                        <td class="text-left x-action bill_col_action cursor-pointer" data-form-id="downpayment-form" data-form="ajax-form"><i class="ti ti-check"></i> </td>
                        <td class="text-left x-rate bill_col_rate"><input type="number" id="dp-subtotal" class="form-control" name="subtotal" value="{{$invoice->subtotal}}"></td>
                        <td class="text-left x-rate"><input type="text" class="form-control" id="dp-description" name="description" value="{{$invoice->description}}"></td>
                        <td class="text-left x-rate bill_col_rate">@money($invoice->subtotal, $invoice->contract->currency, true)</td>
                        </form>
                      </tr>
                    </tbody>
                </table>
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
          </div>
        </div>

        <hr class="my-3 mx-n4" />

        <div class="row px-0 px-sm-4">
          <div class="col-12">
            <div class="mb-3">
              <label for="note" class="form-label fw-semibold">Note:</label>
              <textarea class="form-control" rows="2" name="note" id="note">{{$invoice->note}}</textarea>
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <label for="terms" class="form-label fw-semibold">Terms:</label>
              <textarea class="form-control" rows="2" name="terms" id="terms">{{$invoice->terms}}</textarea>
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
<form id="downpayment-form" action="{{route('admin.invoices.update', [$invoice, 'type' => 'downpayment'])}}">
  @method('PUT')
  <input type="hidded" id="dp-form-subtotal" name="subtotal" value="{{$invoice->subtotal}}">
  <input type="hidded" id="dp-form-description" name="description" value="{{$invoice->description}}">
</form>
</div>
@endsection
