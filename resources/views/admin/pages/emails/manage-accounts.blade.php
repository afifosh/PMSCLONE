@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Manage Accounts')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
@endsection
<script src="{{asset('assets/js/helper.js')}}"></script>
@section('page-script')
@include('admin.pages.emails.partials.scripts')
<script>
  function disableSync(elem, account_id) {
    var url = '';
    if ($(elem).prop('checked') == true) {
      var url = "{{url('/admin/mail/accounts/:accountId/sync/disable')}}";
    } else {
      var url = "{{url('/admin/mail/accounts/:accountId/sync/enable')}}";
    }
    url = url.replace(':accountId', account_id);
    $.ajax({
      url: url,
      method: 'post',
      success: function(response, status) {
        toastr.success(response);

      },
      error: function(response) {
        var message = "";
        if (response.responseJSON.message == undefined) {
          message = errorMesage
        } else {
          message = response.responseJSON.message
        }
        toastr.error(message);
      }
    });
  }

  function makePrimary(account_id) {
    var url = "{{url('/admin/mail/accounts/:accountId/primary')}}";
    url = url.replace(':accountId', account_id);
    $.ajax({
      url: url,
      method: 'put',
      success: function(response, status) {
        toastr.success(response);

      },
      error: function(response) {
        var message = "";
        if (response.responseJSON.message == undefined) {
          message = errorMesage
        } else {
          message = response.responseJSON.message
        }
        toastr.error(message);
      }
    });
  }
</script>
@endsection
@section('content')
<div class="mt-3  col-12">
  <div class="card">
    <div class="card-body">
      <table class="table no-footer" style="text-align:right">
        <thead>
          <tr>
            <td style="text-align:left">
              <h5> Email Accounts</h5>
            </td>
            <td>

            </td>
            <td>

              <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser" onclick="localStorage.setItem('acc_type','shared');" class="btn btn-primary" data-toggle="ajax-modal">Connect Shared Account</button>
            </td>
            <td>
              <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser" onclick="localStorage.setItem('acc_type','personal');" class="btn btn-primary">Connect Personal Account</button>
            </td>
          </tr>
        </thead>
        <tbody>
          @foreach($accounts as $account)
          <tr>
            <td style="text-align:left">
              <a>{{$account->email}}</a>
              <br />
              <span><i class="ti ti-mail" style="vertical-align:top"> </i> {{$account->connection_type}} </span>
            </td>

            <td>
              <span class="switch-label">Primary Account </span>

              <label class="switch">
                <input type="radio" class="switch-input" @if($account->isprimary()) checked @endif onchange="makePrimary({{$account->id}});" name="make_primary" />
                <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
                </span>
              </label>
            </td>
            <td>
              <span class="switch-label">Disable Sync </span>

              <label class="switch">
              
                <input type="checkbox" class="switch-input" @if($account->sync_state!=App\Enums\SyncState::ENABLED) checked @endif onchange="disableSync(this,{{$account->id}});" name="disable_sync" />
                <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
                </span>
              </label>
            </td>
            <td>
              <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></button>
              <div class="dropdown-menu dropdown-menu-end m-0 show" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(425px, 47px);" data-popper-placement="bottom-end" data-popper-reference-hidden="" data-popper-escaped="">
                <a href="javascript:ajaxCanvas('{{url('/admin/mail/accounts/'.$account->id.'/edit')}}','edit-account-modal');" class="dropdown-item">Edit</a>
                <a href="javascript:deleteRecord('delete','{{url('/admin/mail/accounts/'.$account->id.'/delete')}}','');" class="dropdown-item">Delete</a>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@include('admin.pages.emails.partials.connect-account')
<div class="offcanvas offcanvas-xxl offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="edit-account-modal" style="overflow-y:auto;width:50%; background-color:white !important" aria-labelledby="editAccountModal">
  <div></div>
</div>
@endsection
@push('scripts')
@endpush