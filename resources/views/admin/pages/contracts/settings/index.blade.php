@extends('admin/layouts/layoutMaster')

@section('title', 'Contract Settings')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection
@section('content')
@include('admin.pages.contracts.header', ['tab' => 'settings'])
<!-- User Profile Content -->
<div class="row">
  <div class="col-12">
    {{-- notifiable users --}}
    @if ($contract->status != 'Terminated')
      <div class="card mt-3">
        <div class="card-body">
          {{$dataTable->table()}}
        </div>
      </div>
    @endif
    {{-- End notifiable users --}}
    @if ($contract->status != 'Paused' && $contract->status != 'Terminated' && $contract->start_date != null)
      <div class="card mt-2">
        <h5 class="card-header">Pause Contract</h5>
          <div class="card-body">
            <form method="post" action="{{route('admin.contracts.pause', [$contract])}}">
              @method('PUT')
              <div class="row ms-3">
                <div class="row">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="pause_until" value="manual" id="pause-manual" checked>
                    <label class="form-check-label" for="pause-manual">
                      Pause Until I Resume
                    </label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="pause_until" value="custom_date" id="pause-custom">
                    <label class="form-check-label" for="pause-custom">
                      Pause Until a specific date
                    </label>
                  </div>
                  <div class="col-3 d-none pause-durantion">
                    <div class="mb-3">
                      <input type="date" id="custom-date-value" name="custom_date_value" class="form-control flatpickr" data-flatpickr='{"minDate": "today"}' placeholder="Select Date">
                    </div>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="pause_until" value="custom_unit" id="pause-days">
                    <label class="form-check-label" for="pause-days">
                      Pause For
                    </label>
                  </div>
                  <div class="col-3 d-none pause-durantion">
                    <div class="mb-3 d-flex">
                      <span class="w-50">
                        <input type="number" id="unit-value" name="pause_for"class="form-control cusom_resum_parm">
                      </span>
                      <span class="w-50">
                        {!! Form::select('custom_unit', ['Days' => 'Days', 'Weeks' => 'Weeks', 'Months'=> 'Months'], null, ['class' => 'form-select select2 cusom_resum_parm']) !!}
                      </span>
                    </div>
                    <div class="col">
                      <div class="mb-3">
                        <label for="" class="form-label">Will Resume On: </label>
                        <input type="date" name="calculated_resumed_date" id="calculated_resumed_date" readonly class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <button type="button" data-form="ajax-form" class="mt-3 btn btn-primary">Pause Contract</button>
            </form>
          </div>
      </div>
    @endif
    @if ($contract->status == 'Paused')
      <div class="card mt-2">
        <h5 class="card-header">Resume Contract</h5>
          <div class="card-body">
            <form method="post" action="{{route('admin.contracts.pause', [$contract])}}">
              @method('PUT')
              @if ($contract->events->where('event_type', 'Paused')->count())
              @php
                  $lastPaused = $contract->events->where('event_type', 'Paused')->last();
              @endphp
                @if ($lastPaused->modifications['pause_until'] == 'manual')
                  <p>Contract Is paused until you resume it</p>
                  <div class="row ms-3">
                    <div class="row">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="resume_now" id="resume_now">
                        <label class="form-check-label" for="resume_now">
                          Resume Contract Immediately
                        </label>
                      </div>
                    </div>
                  </div>
                @else
                <p>Contract is scheduled to be resumed at</p>
                @endif
              @endif
              <button type="button" data-form="ajax-form" class="mt-3 btn btn-primary">Resume Contract</button>
            </form>
          </div>
      </div>
    @endif
    <div class="card mt-3">
      <h5 class="card-header">Terminate Contract</h5>
      @if ($contract->events->where('event_type', 'Terminated')->count())
        <div class="card-body">
          <div class="mb-3 col-12 mb-0">
            <div class="alert alert-danger">
              <h5 class="alert-heading mb-1">Contract Terminated</h5>
            </div>
          </div>
        </div>
      @else
        <div class="card-body">
          <div class="mb-3 col-12 mb-0">
            <div class="alert alert-warning">
              <h5 class="alert-heading mb-1">Are you sure you want to Terminate the contract?</h5>
              <p class="mb-0">Once you terminated your contract, there is no going back. Please be certain.</p>
            </div>
          </div>
          <form method="post" action="{{route('admin.contracts.terminate', [$contract])}}">
            @method('PUT')
            <div class="row ms-3">
              <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="contract_termination" id="contract-termination" />
                <label class="form-check-label" for="contract-termination">I confirm terminate contract</label>
              </div>
              <div class="d-none el-terminate row">
                <hr>
                <div class="form-check mb-4">
                  <input class="form-check-input" type="radio" name="terminate_date" value="now" id="terminate-now" checked>
                  <label class="form-check-label" for="terminate-now">
                    Terminate Immediately
                  </label>
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="radio" name="terminate_date" value="custom" id="terminate-date">
                  <label class="form-check-label" for="terminate-date">
                    Terminate on a specific date
                  </label>
                </div>
                <div class="mb-3 col-6 d-none">
                  <input type="date" name="custom_date" id="custom-termination-date" class="form-control flatpickr" placeholder="Termination Date" data-flatpickr='{"minDate" : "today"}'>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-6 d-none el-terminate">
                  <label for="termination-reason">Termination Reason</b></label>
                  <input id="termination-reason" class="form-control" type="text" name="reason" placeholder="Termination Reason">
                </div>
                <div class="form-group col-6 d-none el-terminate">
                  <label for="terminate-confirmation-text">Please Type <b>Delete/{{$contract->subject}}</b></label>
                  <input id="terminate-confirmation-text" data-confirm-del="Delete/{{$contract->subject}}" class="form-control" type="text" name="verificatoin-text">
                </div>
              </div>
            </div>
            <button type="button" id="terminate-submit" data-form="ajax-form" class="mt-3 btn btn-primary disabled">Terminate Contract</button>
          </form>
        </div>
        @endif
    </div>
  </div>
</div>
<!--/ User Profile Content -->
@endsection
@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
{{-- custom flatpickr --}}
<script src="{{asset('assets/js/custom/flatpickr.js')}}"></script>
<script>
  // termination form js
  $(document).ready(function () {
    $('#contract-termination').on('change', function() {
      if ($(this).is(':checked')) {
        $('.el-terminate').removeClass('d-none');
      } else {
        $('.el-terminate').addClass('d-none');
      }
    });

    $('#terminate-confirmation-text').on('change focus keyup blur', function() {
      if ($(this).val() == $(this).data('confirm-del')) {
        $('#terminate-submit').removeClass('disabled');
      } else {
        $('#terminate-submit').addClass('disabled');
      }
    });

    $('input[name="terminate_date"]').on('change', function() {
      if ($(this).val() == 'custom') {
        $('#custom-termination-date').parent().removeClass('d-none');
      } else {
        $('#custom-termination-date').parent().addClass('d-none');
      }
    });
    // end termination form js

    // pause Form js
    $('input[name="pause_until"]').on('change', function() {
      if ($(this).val() == 'custom_date') {
        $('#custom-date-value').parents('.pause-durantion').removeClass('d-none');
      } else {
        $('#custom-date-value').parents('.pause-durantion').addClass('d-none');
      }

      if ($(this).val() == 'custom_unit') {
        $('#unit-value').parents('.pause-durantion').removeClass('d-none');
      } else {
        $('#unit-value').parents('.pause-durantion').addClass('d-none');
      }
    });

  });
</script>
@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
        $('.cusom_resum_parm').change(function(){
            var unit = $('select[name="custom_unit"]').val();
            var value = $('input[name="pause_for"]').val();
            var date = new Date();
            if(unit == 'Days'){
                date.setDate(date.getDate() + parseInt(value));
            }else if(unit == 'Weeks'){
                date.setDate(date.getDate() + (parseInt(value) * 7));
            }else if(unit == 'Months'){
                date.setMonth(date.getMonth() + parseInt(value));
            }
            $('#calculated_resumed_date').val(date.toISOString().slice(0,10));
        });
    </script>
@endpush