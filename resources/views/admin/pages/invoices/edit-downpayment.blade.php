@extends('admin/layouts/layoutMaster')

@section('title', 'Edit - Invoice')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
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
  function reloadPhasesList(){
    // reload page
    window.location.reload();
    // $.ajax({
    //   url: route('admin.invoices.invoice-items.index', { invoice: {{$invoice->id}}, mode: 'edit' }),
    //   type: "GET",
    //   success: function(data) {
    //     $('#billing-items-container-header').siblings().remove();
    //     $('#billing-items-container-header').after(data.data.view_data);
    //     $('.invoice-calculations').html(data.data.summary);
    //     $('#balance-summary').html(data.data.balance_summary);
    //     initSelect2()
    //   }
    // });
  }
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
                <tbody id="balance-summary">
                  @include('admin.pages.invoices.balance-summary-downpayment')
                </tbody>
            </table>
          </div>
        </div>

        <hr class="my-3 mx-n4" />

        <div class="source-item pt-4 px-0 px-sm-4">
          @include('admin.pages.invoices.items.items-list')
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
        <button class="btn btn-primary d-grid mt-2 w-100" type="button" data-toggle="ajax-modal" data-title="{{__('Add Payment')}}" data-href="{{route('admin.finances.payments.create',['invoice' => $invoice->id])}}">
          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-currency-dollar ti-xs me-1"></i>Add Payment</span>
        </button>
      </div>
    </div>
  </div>
</form>
<div class="mt-3 col-lg-9 col-12">
  <div class="card">
    <h5 class="card-header">Invoices using this downpayment</h5>
    <div class="card-body">
      {{$dataTable->table()}}
    </div>
  </div>
</div>
<form id="downpayment-form" action="{{route('admin.invoices.update', [$invoice, 'type' => 'downpayment'])}}">
  @method('PUT')
  <input type="hidden" id="dp-form-subtotal" name="subtotal" value="{{$invoice->subtotal}}">
  <input type="hidden" id="dp-form-description" name="description" value="{{$invoice->description}}">
</form>
</div>
@endsection

@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
@endpush
