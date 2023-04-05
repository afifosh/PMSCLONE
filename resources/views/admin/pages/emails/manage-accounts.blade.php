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
@endsection
@section('content')
  <div class="mt-3  col-12">
    <div class="card">
      <div class="card-body">
        <table class="table no-footer">
        <thead>
          <tr>
            <td>
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
              <td></td>
          </tr>
        </thead>
        <tbody>
          @foreach($accounts as $account)
        <tr>
            <td>
            <a>{{$account->email}}</a>
            <br/>
            <span><i class="ti ti-mail" style="vertical-align:top"> </i>  {{$account->connection_type}} </span>
            </td>
            <td>
              
              </td>
              <td>
              <span>Primary Account</span>
              </td>
              <td>
                <span>Disable Sync</span>
              </td>
              <td>
              <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></button>
              <div class="dropdown-menu dropdown-menu-end m-0 show" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(425px, 47px);" data-popper-placement="bottom-end" data-popper-reference-hidden="" data-popper-escaped="">
        <a href="javascript:ajaxCanvas('{{url('/admin/mail/accounts/'.$account->id.'/edit')}}','edit-account-modal');" class="dropdown-item">Edit</a>
                    <a href="javascript:;" class="dropdown-item">Delete</a>
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
  <div class="offcanvas offcanvas-xxl offcanvas-end" tabindex="-1" id="edit-account-modal" style="width:50%; background-color:white !important" aria-labelledby="editAccountModal">
<div></div>
</div>
@endsection
@push('scripts')
@endpush