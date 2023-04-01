@props([
    'identifier' => 'amazon_sesService',
    'provider' => 'amazon_ses',
    'settings' => [],
])

<form method="POST" action="{{ route('admin.setting.delivery.update') }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="provider" value="{{ $provider }}" />

    <div id="{{ $identifier }}" class="deliveryService {{ $identifier==='amazon_sesService' ? 'd-block' : 'd-none' }}">
        <x-admin.settings.delivery.delivery-from-address :$identifier :$settings>
            <!-- no content -->
        </x-admin.settings.delivery.delivery-from-address>

        {{ $slot }}

        <div class="mb-4">
            <button data-form="ajax-form" type="submit" class="btn btn-primary me-sm-3">
                @lang('Update')
            </button>
            <x-ui.clear-form-button class="btn btn-dark">
                {{ __('Clear Form') }}
            </x-ui.clear-form-button>
        </div>

    </div>
</form>