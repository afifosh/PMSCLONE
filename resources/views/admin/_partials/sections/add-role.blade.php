<!-- Add Role Modal -->
@forelse ($modules as $module)
<tr>
  <td class="text-nowrap fw-semibold">{{$module->name}}</td>
  <td>
    <div class="d-flex">
      @forelse ($module->permissions as $permission)
        <div class="form-check {{$loop->last ? '' : 'me-3 me-lg-5'}} ">
          <input class="form-check-input" name="permissions[]" type="checkbox" value="{{$permission->id}}" id="persm-{{$permission->id}}" />
          <label class="form-check-label" for="persm-{{$permission->id}}">
            @if($permission->name == 'Personal Mailbox')
              Personal Account
            @elseif ($permission->name == 'access shared inbox')
              Shared Account
            @else
            {{explode(" ",$permission->name)[0]}}
            @endif
          </label>
        </div>
      @empty
      @endforelse
    </div>
  </td>
</tr>
@empty
@endforelse
<!--/ Add Role Modal -->
