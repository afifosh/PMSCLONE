<div class="row">
    <p>
        <b>Login Information as follows</b>
    </p>
    

    @isset($row['location']['ip'])
    <div class="d-flex">
        <p class="mb-0">
            <b>@lang('ip_address:')</b>
        </p>
        {{ $row['location']['ip'] }}
    </div>
    @endisset


    @isset($row['location']['city'])
    <div class="d-flex">
        <p class="mb-0">
            <b>@lang('city:')</b>
        </p>
        {{ $row['location']['city'] }}
    </div>
    @endisset



    @isset($row['location']['country'])
    <div class="d-flex">
        <p class="mb-0">
            <b>@lang('country:')</b>
        </p>
        {{ $row['location']['country'] }}
    </div>
    @endisset

    @isset($row['location']['timezone'])
    <div class="d-flex">
        <p class="mb-0">
            <b>@lang('timezone:')</b>
        </p>
        {{ $row['location']['timezone'] }}
    </div>
    @endisset


    @isset($row['location']['postal_code'])
    <div class="d-flex">
        <p class="">
            <b>@lang('postal_code:')</b>
        </p>
        {{ $row['location']['postal_code'] }}
    </div>
    @endisset

    <b>@lang('If you do not recognize this activity. Consider changing your credentials')</b>


</div>