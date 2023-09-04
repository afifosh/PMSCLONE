<div class="d-inline-block text-nowrap">
      <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Contact" data-href="{{route('admin.companies.contacts.edit', [$company, $user])}}"><i class="ti ti-edit"></i></button>
      <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
          data-href="{{ route('admin.companies.contacts.destroy', [$company, $user]) }}"><i class="ti ti-trash"></i></button>
</div>
