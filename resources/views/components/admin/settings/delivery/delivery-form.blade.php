@props([
    'identifier' => 'amazon_sesService',
    'provider' => 'amazon_ses',
    'settings' => [],
])

<form method="POST" action="{{ route('admin.core.settings.update-delivery') }}">
    {{-- @csrf
    @method('PUT') --}}
    <input type="hidden" name="provider" value="{{ $provider }}" />

    <div id="{{ $identifier }}" class="deliveryService {{ $identifier==='amazon_sesService' ? 'd-block' : 'd-none' }}">
        <x-admin.settings.delivery.delivery-from-address :$identifier :$settings>
            <!-- no content -->
        </x-admin.settings.delivery.delivery-from-address>

        {{ $slot }}

        <button data-form="ajax-form" type="submit" class="btn btn-primary me-sm-3">
            @lang('Update')
        </button>
        <button data-toggle="ajax-modal" data-title="Send Test Email" data-href="{{route('admin.core.settings.delivery.send-test-email')}}" type="button" class="btn btn-outline-dark me-sm-3">
          @lang('Send Test Email')
      </button>
    </div>
</form>
