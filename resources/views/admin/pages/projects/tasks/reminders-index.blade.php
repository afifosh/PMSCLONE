@forelse ($task->reminders as $reminder)
  <li class="" data-id="4">
    <div class="mbot15">
      <div>
        <p class="bold">Reminder for {{$reminder->recipient->email}} on {{$reminder->remind_at}}
          <a href="#" class="text-danger delete-reminder" onclick="remove_reminder({{$reminder->id}})">
            <i class="fa fa-remove"></i>
          </a>
        </p>{{$reminder->description}}
      </div>
    </div>
  </li>
  <hr>
@empty
@endforelse
