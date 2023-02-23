{!! Form::model($share, [
    'route' => ['admin.draft-rfps.files.shares.revoke', ['draft_rfp' => $share->id, 'file' => $share->rfp_file_id, 'share' => $share->id]],
    'method' => 'POST',
]) !!}

<div class="row">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i> {{ __('Are you sure you want to revoke this share?') }}
        </div>
    </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Revoke') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
