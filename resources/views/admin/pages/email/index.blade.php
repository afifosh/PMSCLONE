@extends('admin/layouts/layoutMaster')

@section('title', 'Email')

@section('vendor-style')
@endsection

@section('head')
    <script>
        // updateTheme();
        config = {!! Js::from(array_merge($config, ['csrfToken' => csrf_token()])) !!};
        var lang = {!! Js::from($lang) !!};
    </script>
    <!-- Add all of the custom registered styles -->
    @foreach (\Modules\Core\Facades\Innoclapps::styles() as $name => $path)
        @if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://']))
            <link rel="stylesheet" href="{!! $path !!}">
        @else
            <link rel="stylesheet" href="{{ url("styles/$name") }}">
        @endif
    @endforeach

    <script>
        window.Innoclapps = {
            bootingCallbacks: [],
            booting: function(callback) {
                this.bootingCallbacks.push(callback)
            }
        }
    </script>
@endsection

@section('content')
<div id="navbar-actions" class="hidden items-center lg:flex"><span class="mx-3 h-navbar border-l border-neutral-200 dark:border-neutral-600 lg:mx-6 hidden lg:block"></span><div class="inline-flex items-center"><div data-headlessui-state="" class=""><button type="button" class="flex items-center rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-neutral-100 text-neutral-600 hover:text-neutral-800 focus:ring-primary-500 dark:text-white dark:hover:text-neutral-400"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="pointer-events-none w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"></path></svg></button></div><button type="button" class="btn btn-secondary btn-sm rounded only-icon ml-3 lg:ml-6" v-placement="left" v-tooltip="Synchronize" v-variant="dark"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="pointer-events-none shrink-0 h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg><!----></button></div></div>
<div class="flex h-screen overflow-hidden bg-neutral-100 dark:bg-neutral-800" id="app" v-cloak>
  <div class="flex w-0 flex-1 flex-col overflow-hidden">
      @if ($alert = get_current_alert())
          <i-alert variant="{{ $alert['variant'] }}" dismissible>
              {{ $alert['message'] }}
          </i-alert>
      @endif
      <router-view></router-view>
      <i-confirmation-dialog v-if="confirmationDialog && !confirmationDialog.injectedInDialog"
          :dialog="confirmationDialog">
      </i-confirmation-dialog>

      {{-- <teleport to="body">
          <float-notifications></float-notifications>
      </teleport> --}}
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('js/mail-client/mail-client.js') }}"></script>
@endsection

@section('page-script')
    @foreach (\Modules\Core\Facades\Innoclapps::scripts() as $name => $path)
        @if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://']))
            <script src="{!! $path !!}"></script>
        @else
            <script src="{{ url("scripts/$name") }}"></script>
        @endif
    @endforeach

    <script defer>
      bootApplication();
        function bootApplication() {
            window.Innoclapps = CreateApplication(config, Innoclapps.bootingCallbacks)
            Innoclapps.start();
            alert('bootApplicationDone')
        }
    </script>
@endsection
