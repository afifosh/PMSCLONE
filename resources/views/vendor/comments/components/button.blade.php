<button
    type="{{ isset($submit) && $submit ? 'submit' : 'button' }}"
    @class([
        // 'comments-button',
        'btn',
        'btn-primary' => !(isset($link) && $link),
        'is-small' => isset($small) && $small,
        'is-danger' => isset($danger) && $danger,
        'is-link btn-outline-primary' => isset($link) && $link,
    ])
    {{ $attributes->except('type', 'size', 'submit') }}
>
    {{ $slot }}
</button>
