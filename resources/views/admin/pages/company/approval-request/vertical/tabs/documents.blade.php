<div class="card w-100">
  <div class="card-body">
    <h5>KYC Documents</h5>
      <hr>
      <div class="row">
        @forelse ($approved_documents as $doc)
          @php
          $doc_original = $doc;
          $modifications = [];
          $isEditable = false;
          $status = 'approved';
          if ($doc->modifications->count()) {
            if($doc_original->modifications[0]->approvals->count() < $level && !$doc_original->modifications[0]->disapprovals->count()){
              $isEditable = true;
              $status = 'partially approved';
              $modifications = transformModifiedData($doc_original->modifications[0]->modifications);
              $doc['modification_id'] = $doc_original->modifications[0]->id;
            }
            $doc = transformModifiedData($doc_original->modifications[0]->modifications) + $doc->toArray();
          }
          if ($doc_original->modifications->count() && $doc_original->modifications[0]->disapprovals->count()) {
            $status = 'rejected';
          }
        @endphp
          @include('admin.pages.company.approval-request.vertical.tabs.components.kycDoc', ['doc' => $doc])
        @empty
        @endforelse

        @forelse ($documents as $doc)
          @php
            $doc_original = $doc;
            $isEditable = true;
            $status = 'pending';
            if(isset($doc) && ($doc->approvals_count >= $level || $doc->disapprovals_count)) {
                $isEditable = false;
                if($doc->approvals_count >= $level){
                  $status = 'approved';
                }elseif ($doc->disapprovals_count) {
                  $status = 'rejected';
                }
            }
            $doc = transformModifiedData($doc->modifications);
            $doc['modification_id'] = $doc_original->id;
          @endphp
          @include('admin.pages.company.approval-request.vertical.tabs.components.kycDoc', ['doc' => $doc])
        @empty
        @endforelse
      </div>
      <div class="row">
        <div class="col-12 d-flex justify-content-between">
          <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'addresses']) }}" class="btn btn-light">Previews</a>
          <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'bank-accounts']) }}" class="btn btn-primary">Next</a>
        </div>
      </div>
    </div>
  </div>
</div>
