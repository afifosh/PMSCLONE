Hi {{$notifiable->first_name}} {{$notifiable->last_name}},<br><br>

Your contract: {{$contract->subject}} is going to expire on {{$contract->end_date}}.<br><br>

<a href="{{route('admin.contracts.show', $contract->id)}}">Click here</a> to view the contract.<br><br>

Thanks,<br>
{{ config('app.name') }}<br>
