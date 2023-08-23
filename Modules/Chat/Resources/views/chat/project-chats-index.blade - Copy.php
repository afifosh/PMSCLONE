@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')
{{-- @extends('chat::layouts.app') --}}
@section('title')
    {{ __('chat::messages.conversations') }}
@endsection
@section('page-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css">
    {{-- <link rel="stylesheet" href="{{ mix('chat/assets/css/coreui.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('chat/assets/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('chat/assets/css/datetime-picker.css') }}"/>
    <link rel="stylesheet" href="{{ mix('chat/assets/css/jquery.toast.min.css') }}">

    @livewireStyles
    @routes
    {{-- <link rel="stylesheet" href="{{ mix('chat/assets/css/font-awesome.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('chat/assets/css/emojionearea.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ mix('chat/assets/css/style.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ mix('chat/assets/css/custom-style.css') }}"> --}}

    <link rel="stylesheet" href="{{ asset('chat/assets/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('chat/assets/css/yBox.min.css') }}">
    <link rel="stylesheet" href="{{ mix('chat/assets/css/video-js.css') }}">
    <link rel="stylesheet" href="{{ mix('chat/assets/css/new-conversation.css') }}">

    <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-chat.css')}}" />
@endsection


@section('content')

<div class="app-chat card overflow-hidden">
    <div class="row g-0">
      <!-- Sidebar Left -->
      <div class="col app-chat-sidebar-left app-sidebar overflow-hidden" id="app-chat-sidebar-left">
        <div class="chat-sidebar-left-user sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-4 pt-5">
          <div class="avatar avatar-xl avatar-online">
            <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
          </div>
          <h5 class="mt-2 mb-0">John Doe</h5>
          <small>Admin</small>
          <i class="ti ti-x ti-sm cursor-pointer close-sidebar" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-left"></i>
        </div>
        <div class="sidebar-body px-4 pb-4">
          <div class="my-4">
            <p class="text-muted text-uppercase">About</p>
            <textarea id="chat-sidebar-left-user-about" class="form-control chat-sidebar-left-user-about mt-3" rows="4" maxlength="120">Dessert chocolate cake lemon drops jujubes. Biscuit cupcake ice cream bear claw brownie brownie marshmallow.</textarea>
          </div>
          <div class="my-4">
            <p class="text-muted text-uppercase">Status</p>
            <div class="d-grid gap-1">
              <div class="form-check form-check-success">
                <input name="chat-user-status" class="form-check-input" type="radio" value="active" id="user-active" checked>
                <label class="form-check-label" for="user-active">Active</label>
              </div>
              <div class="form-check form-check-danger">
                <input name="chat-user-status" class="form-check-input" type="radio" value="busy" id="user-busy">
                <label class="form-check-label" for="user-busy">Busy</label>
              </div>
              <div class="form-check form-check-warning">
                <input name="chat-user-status" class="form-check-input" type="radio" value="away" id="user-away">
                <label class="form-check-label" for="user-away">Away</label>
              </div>
              <div class="form-check form-check-secondary">
                <input name="chat-user-status" class="form-check-input" type="radio" value="offline" id="user-offline">
                <label class="form-check-label" for="user-offline">Offline</label>
              </div>
            </div>
          </div>
          <div class="my-4">
            <p class="text-muted text-uppercase">Settings</p>
            <ul class="list-unstyled d-grid gap-2 me-3">
              <li class="d-flex justify-content-between align-items-center">
                <div>
                  <i class='ti ti-message me-1'></i>
                  <span class="align-middle">Two-step Verification</span>
                </div>
                <label class="switch switch-primary me-4">
                  <input type="checkbox" class="switch-input" checked="" />
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                </label>
              </li>
              <li class="d-flex justify-content-between align-items-center">
                <div>
                  <i class='ti ti-bell me-1'></i>
                  <span class="align-middle">Notification</span>
                </div>
                <label class="switch switch-primary me-4">
                  <input type="checkbox" class="switch-input" />
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                </label>
              </li>
              <li>
                <i class="ti ti-user-plus me-1"></i>
                <span class="align-middle">Invite Friends</span>
              </li>
              <li>
                <i class="ti ti-trash me-1"></i>
                <span class="align-middle">Delete Account</span>
              </li>
            </ul>
          </div>
          <div class="d-flex mt-4">
            <button class="btn btn-primary" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-left">Logout</button>
          </div>
        </div>
      </div>
      <!-- /Sidebar Left-->
  
      <!-- Chat & Contacts -->
      <div class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end" id="app-chat-contacts">
        <div class="sidebar-header">
          <div class="d-flex align-items-center me-3 me-lg-0">
            <div class="flex-shrink-0 avatar avatar-online me-3" data-bs-toggle="sidebar" data-overlay="app-overlay-ex" data-target="#app-chat-sidebar-left">
              <img class="user-avatar rounded-circle cursor-pointer" src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar">
            </div>
            <div class="flex-grow-1 input-group input-group-merge rounded-pill">
              <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
              <input type="text" class="form-control chat-search-input" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon-search31">
            </div>
          </div>
          <i class="ti ti-x cursor-pointer mt-2 me-1 d-lg-none d-block position-absolute top-0 end-0" data-overlay data-bs-toggle="sidebar" data-target="#app-chat-contacts"></i>
        </div>
        <hr class="container-m-nx m-0 d-none">
        <ul class="nav nav-tabs nav-fill mx-0" role="tablist">
            <li class="nav-item" role="presentation">
                <a data-bs-toggle="tab" id="activeChatTab" class="nav-link active login-group__sub-title" href="#chatPeopleBody">{{__('chat::messages.chats.active_chat')}}</a>
            </li>
            
            <li class="nav-item" role="presentation">
                <a data-bs-toggle="tab" id="archiveChatTab" class="nav-link login-group__sub-title" href="#archivePeopleBody">{{__('chat::messages.chats.archive_chat')}}</a>
            </li>
        </ul>

        <div class="sidebar-body">
            <!-- Chats -->
            <ul class="list-unstyled chat-contact-list" id="chat-list">
              <li class="chat-contact-list-item chat-list-item-0 d-none">
                <h6 class="text-muted mb-0">No Chats Found</h6>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-online">
                    <img src="{{asset('assets/img/avatars/13.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Waldemar Mannering</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Refer friends. Get rewards.</p>
                  </div>
                  <small class="text-muted mb-auto">5 Minutes</small>
                </a>
              </li>
              <li class="chat-contact-list-item active">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-offline">
                    <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Felecia Rower</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">I will purchase it for sure. 👍</p>
                  </div>
                  <small class="text-muted mb-auto">30 Minutes</small>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-busy">
                    <span class="avatar-initial rounded-circle bg-label-success">CM</span>
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Calvin Moore</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">If it takes long you can mail inbox user</p>
                  </div>
                  <small class="text-muted mb-auto">1 Day</small>
                </a>
              </li>
            </ul>
            <!-- Contacts -->
            <ul class="list-unstyled chat-contact-list mb-0" id="contact-list">
              <li class="chat-contact-list-item chat-contact-list-item-title">
                <h5 class="text-primary mb-0">Contacts</h5>
              </li>
              <li class="chat-contact-list-item contact-list-item-0 d-none">
                <h6 class="text-muted mb-0">No Contacts Found</h6>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-offline">
                    <img src="{{asset('assets/img/avatars/4.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Natalie Maxwell</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">UI/UX Designer</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-busy">
                    <img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Jess Cook</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Business Analyst</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="avatar d-block flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary">LM</span>
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Louie Mason</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Resource Manager</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-busy">
                    <img src="{{asset('assets/img/avatars/7.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Krystal Norton</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Business Executive</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-offline">
                    <img src="{{asset('assets/img/avatars/8.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Stacy Garrison</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Marketing Ninja</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="avatar d-block flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success">CM</span>
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Calvin Moore</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">UX Engineer</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-busy">
                    <img src="{{asset('assets/img/avatars/10.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Mary Giles</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Account Department</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-offline">
                    <img src="{{asset('assets/img/avatars/13.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Waldemar Mannering</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">AWS Support</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="avatar d-block flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-danger">AJ</span>
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Amy Johnson</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Frontend Developer</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-offline">
                    <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">Felecia Rower</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Cloud Engineer</p>
                  </div>
                </a>
              </li>
              <li class="chat-contact-list-item">
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-busy">
                    <img src="{{asset('assets/img/avatars/11.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-2">
                    <h6 class="chat-contact-name text-truncate m-0">William Stephens</h6>
                    <p class="chat-contact-status text-muted text-truncate mb-0">Backend Developer</p>
                  </div>
                </a>
              </li>
            </ul>
          </div>

      </div>
      <!-- /Chat contacts -->
  
      <!-- Chat History -->
      <div class="col app-chat-history bg-body">
        <div class="chat-history-wrapper">
          <div class="chat-history-header border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex overflow-hidden align-items-center">
                <i class="ti ti-menu-2 ti-sm cursor-pointer d-lg-none d-block me-2" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-contacts"></i>
                <div class="flex-shrink-0 avatar">
                  <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-right">
                </div>
                <div class="chat-contact-info flex-grow-1 ms-2">
                  <h6 class="m-0">Felecia Rower</h6>
                  <small class="user-status text-muted">NextJS developer</small>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <i class="ti ti-phone-call cursor-pointer d-sm-block d-none me-3"></i>
                <i class="ti ti-video cursor-pointer d-sm-block d-none me-3"></i>
                <i class="ti ti-search cursor-pointer d-sm-block d-none me-3"></i>
                <div class="dropdown d-flex align-self-center">
                  <button class="btn p-0" type="button" id="chat-header-actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="chat-header-actions">
                    <a class="dropdown-item" href="javascript:void(0);">View Contact</a>
                    <a class="dropdown-item" href="javascript:void(0);">Mute Notifications</a>
                    <a class="dropdown-item" href="javascript:void(0);">Block Contact</a>
                    <a class="dropdown-item" href="javascript:void(0);">Clear Chat</a>
                    <a class="dropdown-item" href="javascript:void(0);">Report</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="chat-history-body bg-body">
            <ul class="list-unstyled chat-history">
              <li class="chat-message chat-message-right">
                <div class="d-flex overflow-hidden">
                  <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                      <p class="mb-0">How can we help? We're here for you! 😄</p>
                    </div>
                    <div class="text-end text-muted mt-1">
                      <i class='ti ti-checks ti-xs me-1 text-success'></i>
                      <small>10:00 AM</small>
                    </div>
                  </div>
                  <div class="user-avatar flex-shrink-0 ms-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                </div>
              </li>
              <li class="chat-message">
                <div class="d-flex overflow-hidden">
                  <div class="user-avatar flex-shrink-0 me-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                  <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                      <p class="mb-0">Hey John, I am looking for the best admin template.</p>
                      <p class="mb-0">Could you please help me to find it out? 🤔</p>
                    </div>
                    <div class="chat-message-text mt-2">
                      <p class="mb-0">It should be Bootstrap 5 compatible.</p>
                    </div>
                    <div class="text-muted mt-1">
                      <small>10:02 AM</small>
                    </div>
                  </div>
                </div>
              </li>
              <li class="chat-message chat-message-right">
                <div class="d-flex overflow-hidden">
                  <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                      <p class="mb-0">{{ config('variables.templateName') ? config('variables.templateName') : 'TemplateName' }} has all the components you'll ever need in a app.</p>
                    </div>
                    <div class="text-end text-muted mt-1">
                      <i class='ti ti-checks ti-xs me-1 text-success'></i>
                      <small>10:03 AM</small>
                    </div>
                  </div>
                  <div class="user-avatar flex-shrink-0 ms-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                </div>
              </li>
              <li class="chat-message">
                <div class="d-flex overflow-hidden">
                  <div class="user-avatar flex-shrink-0 me-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                  <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                      <p class="mb-0">Looks clean and fresh UI. 😃</p>
                    </div>
                    <div class="chat-message-text mt-2">
                      <p class="mb-0">It's perfect for my next project.</p>
                    </div>
                    <div class="chat-message-text mt-2">
                      <p class="mb-0">How can I purchase it?</p>
                    </div>
                    <div class="text-muted mt-1">
                      <small>10:05 AM</small>
                    </div>
                  </div>
                </div>
              </li>
              <li class="chat-message chat-message-right">
                <div class="d-flex overflow-hidden">
                  <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                      <p class="mb-0">Thanks, you can purchase it.</p>
                    </div>
                    <div class="text-end text-muted mt-1">
                      <i class='ti ti-checks ti-xs me-1 text-success'></i>
                      <small>10:06 AM</small>
                    </div>
                  </div>
                  <div class="user-avatar flex-shrink-0 ms-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                </div>
              </li>
              <li class="chat-message">
                <div class="d-flex overflow-hidden">
                  <div class="user-avatar flex-shrink-0 me-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                  <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                      <p class="mb-0">I will purchase it for sure. 👍</p>
                    </div>
                    <div class="chat-message-text mt-2">
                      <p class="mb-0">Thanks.</p>
                    </div>
                    <div class="text-muted mt-1">
                      <small>10:08 AM</small>
                    </div>
                  </div>
                </div>
              </li>
              <li class="chat-message chat-message-right">
                <div class="d-flex overflow-hidden">
                  <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                      <p class="mb-0">Great, Feel free to get in touch.</p>
                    </div>
                    <div class="text-end text-muted mt-1">
                      <i class='ti ti-checks ti-xs me-1 text-success'></i>
                      <small>10:10 AM</small>
                    </div>
                  </div>
                  <div class="user-avatar flex-shrink-0 ms-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                </div>
              </li>
              <li class="chat-message">
                <div class="d-flex overflow-hidden">
                  <div class="user-avatar flex-shrink-0 me-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                  <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                      <p class="mb-0">Do you have design files for Vuexy?</p>
                    </div>
                    <div class="text-muted mt-1">
                      <small>10:15 AM</small>
                    </div>
                  </div>
                </div>
              </li>
              <li class="chat-message chat-message-right">
                <div class="d-flex overflow-hidden">
                  <div class="chat-message-wrapper flex-grow-1 w-50">
                    <div class="chat-message-text">
                      <p class="mb-0">Yes that's correct documentation file, Design files are included with the template.</p>
                    </div>
                    <div class="text-end text-muted mt-1">
                      <i class='ti ti-checks ti-xs me-1'></i>
                      <small>10:15 AM</small>
                    </div>
                  </div>
                  <div class="user-avatar flex-shrink-0 ms-3">
                    <div class="avatar avatar-sm">
                      <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <!-- Chat message form -->
          <div class="chat-history-footer shadow-sm">
            <form class="form-send-message d-flex justify-content-between align-items-center ">
              <input class="form-control message-input border-0 me-3 shadow-none" placeholder="Type your message here">
              <div class="message-actions d-flex align-items-center">
                <i class="speech-to-text ti ti-microphone ti-sm cursor-pointer"></i>
                <label for="attach-doc" class="form-label mb-0">
                  <i class="ti ti-photo ti-sm cursor-pointer mx-3"></i>
                  <input type="file" id="attach-doc" hidden>
                </label>
                <button class="btn btn-primary d-flex send-msg-btn">
                  <i class="ti ti-send me-md-1 me-0"></i>
                  <span class="align-middle d-md-inline-block d-none">Send</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- /Chat History -->
  
      <!-- Sidebar Right -->
      <div class="col app-chat-sidebar-right app-sidebar overflow-hidden" id="app-chat-sidebar-right">
        <div class="sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-4 pt-5">
          <div class="avatar avatar-xl avatar-online">
            <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle">
          </div>
          <h5 class="mt-2 mb-0">Felecia Rower</h5>
          <span>NextJS Developer</span>
          <i class="ti ti-x ti-sm cursor-pointer close-sidebar d-block" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-right"></i>
        </div>
        <div class="sidebar-body px-4 pb-4">
          <div class="my-4">
            <p class="text-muted text-uppercase">About</p>
            <p class="mb-0 mt-3">A Next. js developer is a software developer who uses the Next. js framework alongside ReactJS to build web applications.</p>
          </div>
          <div class="my-4">
            <p class="text-muted text-uppercase">Personal Information</p>
            <ul class="list-unstyled d-grid gap-2 mt-3">
              <li class="d-flex align-items-center">
                <i class='ti ti-mail'></i>
                <span class="align-middle ms-2">josephGreen@email.com</span>
              </li>
              <li class="d-flex align-items-center">
                <i class='ti ti-phone-call'></i>
                <span class="align-middle ms-2">+1(123) 456 - 7890</span>
              </li>
              <li class="d-flex align-items-center">
                <i class='ti ti-clock'></i>
                <span class="align-middle ms-2">Mon - Fri 10AM - 8PM</span>
              </li>
            </ul>
          </div>
          <div class="mt-4">
            <p class="text-muted text-uppercase">Options</p>
            <ul class="list-unstyled d-grid gap-2 mt-3">
              <li class="cursor-pointer d-flex align-items-center">
                <i class='ti ti-badge'></i>
                <span class="align-middle ms-2">Add Tag</span>
              </li>
              <li class="cursor-pointer d-flex align-items-center">
                <i class='ti ti-star'></i>
                <span class="align-middle ms-2">Important Contact</span>
              </li>
              <li class="cursor-pointer d-flex align-items-center">
                <i class='ti ti-photo'></i>
                <span class="align-middle ms-2">Shared Media</span>
              </li>
              <li class="cursor-pointer d-flex align-items-center">
                <i class='ti ti-trash'></i>
                <span class="align-middle ms-2">Delete Contact</span>
              </li>
              <li class="cursor-pointer d-flex align-items-center">
                <i class='ti ti-ban'></i>
                <span class="align-middle ms-2">Block Contact</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- /Sidebar Right -->
  
      <div class="app-overlay"></div>
    </div>
  </div>














    <div class="page-container">
        <div class="chat-container chat">
            <div class="chat__inner">
                <!-- left section of chat area (chat person selection area) -->
                <div class="chat__people-wrapper chat__people-wrapper--responsive">
                    <div class="chat__people-wrapper-header">
                        <span class="h3 mb-0">{{ __('chat::messages.conversations') }}</span>
                        <div class="d-flex chat__people-wrapper-btn-group ms-1">
                            <i class="nav-icon fa-solid fa-bars align-top chat__people-wrapper-bar"></i>
                            @if($enableGroupSetting == 1)
                                @if(Auth::user()->hasRole('Admin'))
                                    <div
                                        class="chat__people-wrapper-button btn-create-group me-2 d-flex align-items-center"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createNewGroup">
                                        <i class="nav-icon group-icon color-green remove-tooltip" data-bs-toggle="tooltip"
                                           data-bs-placement="bottom"
                                           title="{{ __('chat::messages.create_new_group') }}"><img
                                                    src="{{asset('chat/assets/icons/group.png')}}" width="33" height="33"></i>
                                    </div>
                                @elseif($membersCanAddGroup == 1)
                                    <div
                                        class="chat__people-wrapper-button btn-create-group me-2 d-flex align-items-center"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createNewGroup">
                                        <i class="nav-icon group-icon color-green remove-tooltip" data-bs-toggle="tooltip"
                                           data-bs-placement="bottom"
                                           title="{{ __('chat::messages.create_new_group') }}"><img
                                                    src="{{asset('chat/assets/icons/group.png')}}" width="33" height="33"></i>
                                    </div>
                                @endif
                            @endif
                            {{-- <div class="chat__people-wrapper-button d-flex align-items-center" data-bs-toggle="modal"
                                 data-bs-target="#addNewChat">
                                <i class="nav-icon remove-tooltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                   title="{{ __('chat::messages.new_conversation') }}"><img
                                        src="{{asset('chat/assets/icons/bubble-chat.png')}}" width="30" height="30"></i>
                            </div> --}}
                            <i class="nav-icon fa-solid fa-xmark align-top chat__people-close-bar d-sm-none d-block align-self-center ms-2"></i>
                        </div>
                    </div>
                    <div class="chat__search-wrapper">
                        <div class="chat__search clearfix chat__search--responsive">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="search" placeholder="{{ __('chat::messages.search') }}" class="chat__search-input"
                                   id="searchUserInput">
                            <i class="fa-solid fa-magnifying-glass d-lg-none chat__search-responsive-icon"></i>
                        </div>
                    </div>
                    <ul class="nav nav-tabs chat__tab-nav mb-1 border-bottom-0" id="chatTabs">
                        <li class="nav-item">
                            <a data-bs-toggle="tab" id="activeChatTab" class="nav-link active login-group__sub-title" href="#chatPeopleBody">{{__('chat::messages.chats.active_chat')}}</a>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="tab" id="archiveChatTab" class="nav-link login-group__sub-title" href="#archivePeopleBody">{{__('chat::messages.chats.archive_chat')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content chat__tab-content">
                        <div class="chat__people-body tab-pane fade in active show" id="chatPeopleBody">
                            <div id="infyLoader" class="infy-loader chat__people-body-loader">
                                @include('chat::partials.infy-loader')
                            </div>
                            <div class="text-center no-conversation" style="display: none">
                                <div class="chat__no-conversation">
                                    <div class="text-center"><i class="fa-solid fa-2x fa-comment" aria-hidden="true"></i></div>
                                    {{ __('chat::messages.no_conversation_found') }}
                                </div>
                            </div>
                            <div class="text-center no-conversation-yet" style="display: none">
                                <div class="chat__no-conversation">
                                    <div class="text-center"><i class="fa-solid fa-2x fa-comment" aria-hidden="true"></i></div>
                                    {{ __('chat::messages.no_conversation_added_yet') }}
                                </div>
                            </div>
                            <div id="loadMoreConversationBtn" style="display: none">
                                <a href="javascript:void(0)" class="load-more-conversation">Load More</a>
                            </div>
                        </div>
                        <div class="chat__people-body tab-pane" id="archivePeopleBody">
                            <div class="text-center no-archive-conversation">
                                <div class="chat__no-archive-conversation">
                                    <div class="text-center"><i class="fa-solid fa-2x fa-comment" aria-hidden="true"></i></div>
                                    {{ __('chat::messages.no_conversation_found') }}
                                </div>
                            </div>
                            <div class="text-center no-archive-conversation-yet">
                                <div class="chat__no-archive-conversation">
                                    <div class="text-center"><i class="fa-solid fa-2x fa-comment" aria-hidden="true"></i></div>
                                    {{ __('chat::messages.no_conversation_added_yet') }}
                                </div>
                            </div>
                            <div id="loadMoreArchiverConversationBtn" style="display: none">
                                <a href="javascript:void(0)" class="load-more-archive-conversation">{{__('chat::messages.chats.load_more')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ left section of chat area -->
                <!-- right section of chat area (chat conversation area)-->
                <div class="chat__area-wrapper ms-lg-3">
                    @include('chat::chat.no-chat')
                </div>
                <!--/ right section of chat area-->
                <!-- profile section (chat profile section)-->
            @include('chat::chat.chat_profile')
            @include('chat::chat.msg_info')
            <!--/ profile section -->
            </div>
        </div>
        <!-- Modal -->
        <div id="addNewChat" class="modal fade" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered conversation-modal">
                <!-- Modal content-->
                <div class="modal-content modal-new-conversation">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            </i>{{__('chat::messages.group.new_conversations')}} @if($enableGroupSetting == 1) / {{__('chat::messages.group.groups')}} @endif</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            &times;
                        </button>
                    </div>
                    <div class="modal-body">
                        <nav class="nav nav-pills d-flex" id="myTab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-my-contacts-tab" data-bs-toggle="tab"
                               href="#nav-my-contacts" role="tab" aria-controls="nav-my-contacts-tab"
                               aria-expanded="true"> </i>{{ __('chat::messages.my_contacts') }}
                            </a>
                            <a class="nav-item nav-link wrap-text" id="nav-users-tab" data-bs-toggle="tab"
                                    href="#nav-users" role="tab" aria-controls="nav-users" aria-expanded="true">
                                </i>{{ __('chat::messages.new_conversation') }}
                            </a>
                            @if($enableGroupSetting == 1)
                            <a class="nav-item nav-link" id="nav-groups-tab" data-bs-toggle="tab" href="#nav-groups"
                                    role="tab" aria-controls="nav-groups">{{ __('chat::messages.group.groups') }}</a>
                            @endif
                                <a class="nav-item nav-link" id="nav-blocked-users-tab" data-bs-toggle="tab"
                                    href="#nav-blocked-users" role="tab"
                                    aria-controls="nav-blocked-users">{{ __('chat::messages.blocked_users') }}</a>
                        </nav>

                        {{-- <div class="tab-content search-any-member mt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-my-contacts" role="tabpanel"
                                 aria-labelledby="nav-my-contacts-tab">
                                @livewire('chat::my-contacts-search', ['myContactIds' => $myContactIds, 'blockUserIds' => $blockUserIds])
                            </div>
                            <div class="tab-pane fade" id="nav-users" role="tabpanel" aria-labelledby="nav-users-tab">
                                @livewire('chat::search-users', ['myContactIds' => $myContactIds, 'blockUserIds' => $blockUserIds])
                            </div>
                            @if($enableGroupSetting == 1)
                            <div class="tab-pane fade" id="nav-groups" role="tabpanel" aria-labelledby="nav-groups-tab">
                                @livewire('chat::group-search')
                            </div>
                            @endif
                            <div class="tab-pane fade show" id="nav-blocked-users" role="tabpanel"
                                 aria-labelledby="nav-blocked-users-tab">
                                @livewire('chat::blocked-user-search', ['blockedByMeUserIds' => $blockedByMeUserIds])
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        @include('chat::chat.group_modals')
        @include('chat::chat.edit_group_modals')
        @include('chat::chat.report_user_modal')
    </div>
    @include('chat::chat.templates.conversation-template')
    @include('chat::chat.templates.message')
    @include('chat::chat.templates.no-messages-yet')
    @include('chat::chat.templates.no-conversation')
    @include('chat::chat.templates.group_details')
    @include('chat::chat.templates.user_details')
    @include('chat::chat.templates.group_listing')
    @include('chat::chat.templates.group_members')
    @include('chat::chat.templates.single_group_member')
    @include('chat::chat.group_members_modal')
    @include('chat::chat.templates.blocked_users_list')
    @include('chat::chat.templates.add_chat_users_list')
    @include('chat::chat.templates.badge_message_template')
    @include('chat::chat.templates.member_options')
    @include('chat::chat.templates.single_message')
    @include('chat::chat.templates.contact_template')
    @include('chat::chat.templates.conversations_list')
    @include('chat::chat.templates.common_templates')
    @include('chat::chat.templates.my_contacts_listing')
    @include('chat::chat.templates.conversation-request')
    @include('chat::chat.copyImageModal')
    @include('chat::partials.file-upload')
    @include('chat::partials.set_custom_status_modal')
@endsection
@section('page-script')

    <script src="{{asset('assets/js/app-chat.js')}}"></script>

    <script src="{{ asset('chat/assets/js/dropzone.min.js') }}"></script>
    <script src="{{ asset('chat/assets/js/directive.min.js') }}"></script>
    <script src="{{ asset('chat/assets/js/yBox.min.js') }}"></script>
    <script src="{{ mix('chat/assets/js/video.min.js') }}"></script>
    <!--custom js-->
    <script>
        let userURL = '{{url('admin/chat/users')}}/'
        let userListURL = '{{url('admin/chat/users-list')}}' // not use in anywhere
        let chatSelected = false
        let csrfToken = '{{csrf_token()}}'
        let authUserName = '{{ getLoggedInUser()->full_name }}'
        let authImgURL = '{{ getLoggedInUser()->photo_url}}'
        let deleteConversationUrl = '{{url('admin/chat/conversations')}}/'
        let getUsers = '{{url('admin/chat/get-users')}}'  //not used in anywhere
        let appName = '{{ getAppName() }}'
        let conversationId = '{{ $conversationId }}'
        let enableGroupSetting = '{{ isGroupChatEnabled() }}'
        let authRole = "{{ Auth::user()->role_name }}"

        /** Icons URL */
        let pdfURL = '{{ asset('chat/assets/icons/pdf.png') }}'
        let xlsURL = '{{ asset('chat/assets/icons/xls.png') }}'
        let textURL = '{{ asset('chat/assets/icons/text.png') }}';
        let docsURL = '{{ asset('chat/assets/icons/docs.png') }}'
        let videoURL = '{{ asset('chat/assets/icons/video.png') }}'
        let youtubeURL = '{{ asset('chat/assets/icons/youtube.png') }}'
        let audioURL = '{{ asset('chat/assets/icons/audio.png') }}'
        let zipURL = '{{ asset('chat/assets/icons/zip.png') }}'
        let isUTCTimezone = '{{(config('app.timezone') == 'UTC') ? 1  :0 }}'
        let timeZone = '{{config('app.timezone')}}'
        let blockedUsersListObj = JSON.parse('{!! json_encode($blockUserIds) !!}')
        let myContactIdsObj = JSON.parse('{!! json_encode($myContactIds) !!}')
        let groupMembers = []
        let checkShowNameChat = "{{ checkShowNameChat() }}"
        window.conversationType = '{{ $conversationType }}'
    </script>
    <script src="{{ mix('chat/assets/js/chat.js') }}"></script>
@endsection


@section('vendor-script')

<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>


<script src="{{ asset('messages.js') }}"></script>
{{-- <script src="{{ asset('chat/assets/js/bootstrap.bundle.min.js') }}"></script> --}}
<script src="{{ mix('chat/assets/js/coreui.min.js') }}"></script>
<script src="{{ mix('chat/assets/js/jquery.toast.min.js') }}"></script>
<script src="{{ mix('chat/assets/js/sweetalert2.all.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
<script src="{{ asset('chat/assets/js/moment.min.js') }}"></script>
<script src="{{ asset('chat/assets/js/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('chat/assets/js/moment-timezone.min.js') }}"></script>
<script src="{{ asset('chat/assets/icheck/icheck.min.js') }}"></script>
<script src="https://www.jsviews.com/download/jsviews.min.js"></script>
<script src="{{ asset('chat/assets/js/emojionearea.js') }}"></script>
<script src="{{ mix('chat/assets/js/emojione.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('chat/assets/js/datetime-picker.js') }}"></script>
<script src="{{ asset('chat/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('chat/assets/js/jquery.textcomplete.js') }}"></script>
<script>
    $(document).ready(function () {
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    })
    // if (!navigator.serviceWorker.controller) {
    //     navigator.serviceWorker.register('sw.js').then(function (reg) {
    //         console.log('Service worker has been registered for scope: ' + reg.scope)
    //     })
    // }
</script>
<script>
    let currentLocale = "{{ Config::get('app.locale') }}"
    if (currentLocale == '') {
        currentLocale = 'en'
    }
    Lang.setLocale(currentLocale)
    let pusherKey = '{{ config('broadcasting.connections.pusher.key') }}'
    let pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}'
    let messageDeleteTime = '{{ config('configurable.delete_message_time') }}'
    let baseURL = '{{ url('/') }}'
    let conversationsURL = '{{ route('admin.chat.conversations') }}'
    let notifications = JSON.parse(JSON.stringify({!! json_encode(getNotifications()) !!}))
    let loggedInUserId = '{{ getLoggedInUserId() }}'
    let loggedInUserStatus = '{!! getLoggedInUser()->userStatus !!}'
    if (loggedInUserStatus != '') {
        loggedInUserStatus = JSON.parse(JSON.stringify({!! getLoggedInUser()->userStatus !!}))
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });
    (function ($) {
        $.fn.button = function (action) {
            if (action === 'loading' && this.data('loading-text')) {
                this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true)
            }
            if (action === 'reset' && this.data('original-text')) {
                this.html(this.data('original-text')).prop('disabled', false);
            }
        };
    }(jQuery));
    $(document).ready(function () {
        $('.alert').delay(4000).slideUp(300);
    });
</script>
<script src="{{ mix('chat/assets/js/app.js') }}"></script>
<script src="{{ mix('chat/assets/js/custom.js') }}"></script>
<script src="{{ mix('chat/assets/js/notification.js') }}"></script>
<script src="{{ mix('chat/assets/js/set_user_status.js') }}"></script>
<script src="{{mix('chat/assets/js/profile.js')}}"></script>


@livewireScripts
@endsection
