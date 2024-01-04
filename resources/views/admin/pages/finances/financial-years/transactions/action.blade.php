<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{route('admin.finances.financial-years.transactions.destroy', ['financial_year' => '0', 'transaction' => $transaction])}}"><i class="ti ti-trash"></i></button>
  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
    class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
    <a href="javascript:;" data-toggle="ajax-modal" data-title="{{__('Transaction Details')}}" data-href="{{route('admin.finances.financial-years.transactions.show', ['financial_year' => '0', 'transaction' => $transaction])}}" class="dropdown-item">{{__('View')}}</a>
  </div>
</div>
