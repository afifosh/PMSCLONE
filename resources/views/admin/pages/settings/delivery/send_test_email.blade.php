<form action="{{ route('admin.core.settings.delivery.send-test-email') }}" method="POST">
    <div class="row">
        <div class="col-md-6 mb-4">
            <label class="form-label fs-6 mb-2 fw-semibold">
                @lang('Email Address')
            </label>
            <input value="" name="email" type="email" class="form-control" placeholder="@lang('Enter Email Address')" />
        </div>

        <div class="col-md-6 mb-4">
            <label class="form-label fs-6 mb-2 fw-semibold">
                @lang('Subject')
            </label>
            <input value="" name="subject" type="text" class="form-control" placeholder="@lang('Enter Subject')" />
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Message</label>
            <textarea class="form-control" name="message" placeholder="@lang('Enter Message')" rows="3"></textarea>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <button data-form="ajax-form" type="submit" class="btn btn-primary me-sm-3">
            @lang('Send')
        </button>
    </div>

</form>
