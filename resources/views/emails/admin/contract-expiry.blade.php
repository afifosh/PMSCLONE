Hi {{$notifiable->first_name}} {{$notifiable->last_name}},

Your contract: {{$contract->subject}} is going to expire on {{$contract->end_date}}.

<a href="{{route('admin.contracts.show', $contract->id)}}">Click here</a> to view the contract.

Thanks,
{{ config('app.name') }}
