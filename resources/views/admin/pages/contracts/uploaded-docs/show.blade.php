<div class="row g-3">
  {{-- title --}}
  <div class="col-6">
    <span class="fw-bold">Title : </span> {{ $document->title }}
  </div>
  <div class="col-6">
    <span class="fw-bold">Uploaded At : </span> {{ $document->created_at->format('d M Y') }}
  </div>
  <div class="col-6">
    <span class="fw-bold">Uploaded By : </span> {{ $doc->uploader->name ?? 'N/A' }}
  </div>
  <div class="col-6">
    <span class="fw-bold">Status : </span> <span class="badge bg-label-{{($doc->status == 'Active' ? 'success' : 'danger') }}">{{ $doc->status }}</span>
  </div>
  <hr>
  @foreach ($document->fields as $index => $field)
    @forelse (@$doc['fields'] ?? [] as $submited_field)
      @php
        if($submited_field['id'] == $field['id']){
          $field['value'] = $submited_field['value'];
        }
      @endphp
    @empty
    @endforelse
    @if ($field['type'] == 'textarea')
      <div class="form-group col-12 mt-2">
        <label for="fields_{{ $loop->index }}" class="form-label">{{ $field['label'] }}</label>
        <textarea name="fields[{{$field['id']}}]" id="fields_{{ $loop->index }}" class="form-control h-25" required></textarea>
      </div>
    @elseif($field['type'] == 'file')
    <div class="col-12 mt-2" data-file-path="{{@$field['value'] ? getAssetUrl(@$field['value']) : ''}}">
      <div class="">
        <span class="fw-bold">{{$field['label']}}</span>
        @if (@$field['value'])
          <a target="_BLANK" href="{{@$field['value'] ? getAssetUrl(@$field['value']) : ''}}">View</a>
        @endif
      </div>
    </div>
    @else
      <div class="form-group col-12 mt-2">
        <label for="fields_{{ $loop->index }}" class="form-label fw-bold">{{ $field['label'] }}</label>
        <input disabled type="{{ $field['type'] }}" name="fields[{{$field['id']}}]" value="{{@$field['value']}}" id="fields_{{ $loop->index }}"
            class="form-control" @if ($field['type'] == 'file') accept=".jpg,.jpeg,.png" @endif required>
      </div>
    @endif
  @endforeach
  @if ($document->is_expirable)
    <div class="form-group col-12 mt-2">
      <label for="" class="form-label fw-bold">{{ $document->expiry_date_title }}</label>
      <input disabled type="date" name="expiry_date" value="{{@$doc['expiry_date'] ? date('Y-m-d', strtotime($doc['expiry_date'])) : ''}}" class="form-control">
    </div>
  @endif
    <div class="col-12 d-flex justify-content-end">
      <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('Close') }}</button>
    </div>
</div>
