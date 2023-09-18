<div class="d-inline-block text-nowrap">
  {{-- <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Make Transaction" data-href="{{route('admin.finances.financial-years.transactions.create', $financialYear)}}"><i class="fa-solid fa-lg fa-plus-minus"></i></button> --}}
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Financial Year" data-href="{{route('admin.finances.financial-years.edit', $financialYear)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.finances.financial-years.destroy', $financialYear) }}"><i class="ti ti-trash"></i></button>
</div>
