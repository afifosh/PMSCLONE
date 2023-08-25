Hi {{$notifiable->first_name}} {{$notifiable->last_name}},<br>
Your contract: {{$contract->subject}} is terminated.<br>
<br>
<a href="{{route('admin.contracts.show', $contract->id)}}">Click here</a> to view the contract.<br>
<br>
Thanks,<br>
{{ config('app.name') }}<br>
