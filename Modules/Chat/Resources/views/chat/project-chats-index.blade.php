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
    <link rel="stylesheet" href="{{ mix('chat/assets/css/coreui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('chat/assets/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('chat/assets/css/datetime-picker.css') }}"/>
    <link rel="stylesheet" href="{{ mix('chat/assets/css/jquery.toast.min.css') }}">

    @livewireStyles
    @routes
    {{-- <link rel="stylesheet" href="{{ mix('chat/assets/css/font-awesome.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('chat/assets/css/emojionearea.min.css') }}">
    <link rel="stylesheet" href="{{ mix('chat/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ mix('chat/assets/css/custom-style.css') }}">

    <link rel="stylesheet" href="{{ asset('chat/assets/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('chat/assets/css/yBox.min.css') }}">
    <link rel="stylesheet" href="{{ mix('chat/assets/css/video-js.css') }}">
    <link rel="stylesheet" href="{{ mix('chat/assets/css/new-conversation.css') }}">
@endsection
@section('content')
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
                                                    src="{{asset('assets/icons/group.png')}}" width="33" height="33"></i>
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
                        <div class="chat__people-body tab-pane fade in active" id="archivePeopleBody">
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
