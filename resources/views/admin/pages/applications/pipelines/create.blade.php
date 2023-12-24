@if ($pipeline->id)
    {!! Form::model($pipeline, ['route' => ['admin.applications.settings.pipelines.update', $pipeline->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($pipeline, ['route' => ['admin.applications.settings.pipelines.store'], 'method' => 'POST']) !!}
@endif

<div class="row repeater" >
    <div class="form-group col-12">
        {{ Form::label('name', __('Type Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Type Name')]) !!}
    </div>
    <div>
      <hr>
      <span>Stages</span>
      <button type="button" class="btn btn-sm btn-primary mb-2" data-repeater-create>
        <i class="fas fa-plus-circle"></i>
      </button>
    </div>
    {{-- <div > --}}
      <div data-repeater-list="stages" id="stages-container">
        @forelse ($pipeline->stages ?? [] as $stage)
          <div class="d-flex" data-repeater-item data-id="{{$stage->id}}">
            <span class="bi-drag pt-1 cursor-grab me-2"><i class="ti ti-menu-2"></i></span>
            <div class="input-group mb-2">
              <div class="input-group-text">
                <input name="default_stage" class="form-check-input default_stage_radio mt-0" type="radio" value="{{$stage->id}}">
              </div>
              {!! Form::text('stages[][name]', $stage->name, ['class' => 'form-control', 'placeholder' => __('Stage Name')]) !!}
              <button class="btn btn-primary waves-effect" type="button" data-repeater-delete><i class="fas fa-times-circle"></i></button>
            </div>
          </div>
        @empty
          <div class="d-flex" data-repeater-item>
            <span class="bi-drag pt-1 cursor-grab me-2"><i class="ti ti-menu-2"></i></span>
            <div class="input-group mb-2">
              <div class="input-group-text">
                <input name="default_stage" class="form-check-input default_stage_radio mt-0" type="radio" checked value="1">
              </div>
              {!! Form::text('stages[][name]', null, ['class' => 'form-control', 'placeholder' => __('Stage Name')]) !!}
              <button class="btn btn-primary waves-effect" type="button" data-repeater-delete><i class="fas fa-times-circle"></i></button>
            </div>
          </div>
        @endforelse
      </div>
    {{-- </div> --}}
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
<script>
  $(document).on('click', '.default_stage_radio', function(){
    $('.default_stage_radio').prop('checked', false);
    $(this).prop('checked', true);
  })
</script>
