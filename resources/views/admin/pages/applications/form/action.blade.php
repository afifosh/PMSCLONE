@can('edit form')
    @if ($form->json)
        @if ($form->is_active)
            @php
                $hashids = new Hashids('', 20);
                $id = $hashids->encodeHex($form->id);
            @endphp
            @can('theme-setting-form')
                <a class="text-white btn btn-secondary btn-sm" href="{{ route('admin.applications.settings.form.theme', $form->id) }}" data-bs-toggle="tooltip"
                    data-bs-placement="bottom" data-bs-original-title="{{ __('Theme Setting') }}"><i
                        class="ti ti-layout-2"></i></a>
            @endcan
            @can('manage-form-rule')
            <a class="text-white btn btn-secondary btn-sm" href="{{ route('admin.applications.settings.form.rules', $form->id) }}"
                data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Conditional Rules') }}"><i class="ti ti-notebook"></i></a>
            @endcan
            <a class="btn btn-primary btn-sm embed_form" href="javascript:void(0)"
                onclick="copyToClipboard('#embed-form-{{ $form->id }}')" id="embed-form-{{ $form->id }}"
                data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Embedded form') }}"
                data-url='<iframe src="{{ route('admin.applications.settings.forms.survey', $id) }}" scrolling="auto" align="bottom" height:100vh; width="100%></iframe>'><i
                    class="ti ti-code"></i></a>

            <a class="btn btn-success btn-sm copy_form" onclick="copyToClipboard('#copy-form-{{ $form->id }}')"
                href="javascript:void(0)" id="copy-form-{{ $form->id }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Copy Form URL') }}"
                data-url="{{ route('admin.applications.settings.forms.survey', $id) }}"><i class="ti ti-copy"></i></a>

            <a class="text-white btn btn-info btn-sm cust_btn" data-share="{{ route('admin.applications.settings.forms.survey.qr', $id) }}"
                id="share-qr-code" data-bs-toggle="tooltip" data-bs-placement="bottom"
                data-bs-original-title="{{ __('Show QR Code') }}"><i class="ti ti-qrcode"></i></a>
        @endif
    @endif
@endcan
@can('fill form')
    @if ($form->json)
        <a class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Fill Form') }}" href="{{ route('admin.applications.settings.forms.fill', $form->id) }}"><i
                class="ti ti-list"></i></a>
    @endif
@endcan
@can('duplicate form')
    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Duplicate Form') }}"
        onclick="document.getElementById('duplicate-form-{{ $form->id }}').submit();"><i
            class="ti ti-squares-diagonal"></i></a>
@endcan
@can('design form')
    <a class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Design Form') }}" href="{{ route('admin.applications.settings.forms.design', $form->id) }}"><i
            class="ti ti-brush"></i></a>
@endcan
@can('edit form')
    <a class="btn btn-primary btn-sm" href="{{ route('admin.applications.settings.forms.edit', $form->id) }}" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Edit Form') }}" id="edit-form"><i
            class="ti ti-edit"></i></a>
@endcan
@can('delete form')
    <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Delete') }}" data-toggle="ajax-delete" data-href="{{route('admin.applications.settings.forms.destroy', ['form' => $form->id])}}"><i
            class="mr-0 ti ti-trash"></i></a>
@endcan
@can('duplicate form')
    {!! Form::open(['method' => 'POST', 'route' => ['admin.applications.settings.forms.duplicate'], 'id' => 'duplicate-form-' . $form->id]) !!}
    {!! Form::hidden('form_id', $form->id, []) !!}
    {!! Form::close() !!}
@endcan
