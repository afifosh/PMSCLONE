<div class="d-inline-block text-nowrap">
        @if (request()->program->id == $programUser->program_id)
            <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
                data-href="{{ route('admin.programs.users.destroy', ['program' => $programUser->program_id, 'user' => $programUser->admin_id]) }}"><i
                    class="ti ti-trash"></i></button>
        @endif
</div>
