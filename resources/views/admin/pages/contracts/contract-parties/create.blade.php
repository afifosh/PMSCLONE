@if ($contractParty->id)
    {!! Form::model($contractParty, ['route' => ['admin.contracts.contract-parties.update', ['contract' => $contract->id, 'contract_party' => $contractParty->id]], 'method' => 'PUT']) !!}
@else
    {!! Form::model($contractParty, ['route' => ['admin.contracts.contract-parties.store', $contract->id], 'method' => 'POST']) !!}
@endif

<div class="row">
  {{-- contract_party_type --}}
  <div class="form-group col-6">
    {{ Form::label('contract_party_type', __('Party Type'), ['class' => 'col-form-label']) }}
    {!! Form::select('contract_party_type', [''=> 'Select Type', 'Company' => 'Company', 'PartnerCompany' => 'Partner', 'Client' => 'Client'], $partyType ?? null, [
    'class' => 'form-select globalOfSelect2 dependent-select',
    'id' => 'contract-party-type-id',
    ]) !!}
  </div>
  {{-- Contract Party --}}
  <div class="form-group col-6">
    {{ Form::label('contract_party_id', __('Contract Party'), ['class' => 'col-form-label']) }}
    {!! Form::select('contract_party_id', $contractParties ?? [], $contractParty->contract_party_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Owner']),
    'data-dependent_id' => 'contract-party-type-id',
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select Party')
    ]) !!}
  </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
