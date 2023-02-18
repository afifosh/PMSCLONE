<div class="row">
    <p>
        <b>Login Information as follows</b>
    </p>
    

    @isset($data['location']['ip'])
    <div class="d-flex">
        <p class="mb-0">
            <b>@lang('ip_address:')</b>
        </p>
        {{ $data['location']['ip'] }}
    </div>
    @endisset


    @isset($data['location']['city'])
    <div class="d-flex">
        <p class="mb-0">
            <b>@lang('city:')</b>
        </p>
        {{ $data['location']['city'] }}
    </div>
    @endisset



    @isset($data['location']['country'])
    <div class="d-flex">
        <p class="mb-0">
            <b>@lang('country:')</b>
        </p>
        {{ $data['location']['country'] }}
    </div>
    @endisset

    @isset($data['location']['timezone'])
    <div class="d-flex">
        <p class="mb-0">
            <b>@lang('timezone:')</b>
        </p>
        {{ $data['location']['timezone'] }}
    </div>
    @endisset


    @isset($data['location']['postal_code'])
    <div class="d-flex">
        <p class="">
            <b>@lang('postal_code:')</b>
        </p>
        {{ $data['location']['postal_code'] }}
    </div>
    @endisset

    <b>@lang('If you do not recognize this activity. Consider changing your credentials')</b>


</div>