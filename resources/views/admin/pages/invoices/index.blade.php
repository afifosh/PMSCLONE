@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Invoices')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection

@section('content')

@includeWhen(isset($contract),'admin.pages.contracts.header', ['tab' => 'invoices'])
@includeWhen(isset($company),'admin.pages.company.header', ['tab' => 'invoices'])
{{-- Include Default Header --}}
@includeWhen(!isset($contract) && !isset($company), 'admin.pages.invoices.header')

  <div class="mt-3  col-12">
    <div class="card">
      <h5 class="card-header">Search Filter</h5>
      @if (!isset($contract) && !isset($company))
        <form class="js-datatable-filter-form">
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
            <div class="col-4">
              {!! Form::label('filter_contract', 'Contract') !!}
              {!! Form::select('filter_contract', $contracts, '', ['class' => 'form-select select2']) !!}
            </div>
            <div class="col-4">
              {!! Form::label('filter_company', 'Client') !!}
              <select name="filter_company" id="" class="form-select select2">
                <option value="0">All</option>
                @if ($companies->where('type', 'Company')->count() > 0)
                  <optgroup label="Companies">
                    @forelse ($companies->where('type', 'Company') as $comp)
                      <option value="{{$comp->id}}">{{$comp->name}}</option>
                    @empty
                    @endforelse
                  </optgroup>
                @endif
                @if ($companies->where('type', 'Person')->count() > 0)
                  <optgroup label="Person">
                    @forelse ($companies->where('type', 'Person') as $comp)
                      <option value="{{$comp->id}}">{{$comp->name}}</option>
                    @empty
                    @endforelse
                  </optgroup>
                @endif
              </select>
            </div>
          </div>
        </form>
      @endif
      <div class="card-body">
        {{$dataTable->table()}}
      </div>
    </div>
  </div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      $(document).ready(function () {
          $('.js-datatable-filter-form :input').on('change', function (e) {
              window.LaravelDataTables["invoices-table"].draw();
          });

          $('#invoices-table').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
