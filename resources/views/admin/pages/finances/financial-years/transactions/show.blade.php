<div class="card-body">
    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
        <p class="mb-0 text-muted f-14 w-30 text-capitalize">Account Name : </p>
        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">{{$transaction->accountBalance->name}}</p>
    </div>
    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
        <p class="mb-0 text-muted f-14 w-30 text-capitalize">Account Number : </p>
        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">{{$transaction->accountBalance->printableAccountNumber()}}</p>
    </div>

    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
        <p class="mb-0 text-muted f-14 w-30 text-capitalize">Account Currency : </p>
        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">{{$transaction->accountBalance->currency}}</p>
    </div>
    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
        <p class="mb-0 text-muted f-14 w-30 text-capitalize">Amount : </p>
        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">{{$transaction->printableAmount()}}</p>
    </div>

    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
      <p class="mb-0 text-muted f-14 w-30 text-capitalize">New Balance : </p>
      <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">{{$transaction->printableBalance()}}</p>
    </div>

    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
        <p class="mb-0 text-muted f-14 w-30 text-capitalize">Transaction Type : </p>
        <div class="mb-0 text-dark-grey f-14 w-70 text-wrap ql-editor p-0"><span
                class="badge bg-label-{{$transaction->type == 'Debit' ? 'danger' : 'success'}}">{{$transaction->type}}</span></div>
    </div>

    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
        <p class="mb-0 text-muted f-14 w-30 text-capitalize">Transaction Date : </p>
        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">{{$transaction->created_at}}</p>
    </div>

    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
      <p class="mb-0 text-muted f-14 w-30 text-capitalize">Title : </p>
      <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">{{$transaction->title}}</p>
  </div>
  {{-- {{dd($transaction->related)}} --}}
  @if ($transaction->related != null)
    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
      <p class="mb-0 text-muted f-14 w-30 text-capitalize">Related : </p>
      @if ($transaction->related_type == 'App\Models\Contract')
        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap"><a href="{{route('admin.contracts.show', $transaction->related->id)}}" target="_blank">{{$transaction->related->subject}}</a></p>
      @endif
    </div>
  @endif

    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
        <p class="mb-0 text-muted f-14 w-30 text-capitalize">Note : </p>
        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">{{$transaction->description}}</p>
    </div>
</div>
