<table class="table no-footer">
    <thead>
        <tr>
            <th>Id</th>
            <th>Transaction Id</th>
            <th>Amount</th>
            <th>Created At</th>
        </tr>
        <tr>
            @forelse ($invoice->payments as $payment)
                <td>{{runtimeTransIdFormat($payment->id)}}</td>
                <td>{{$payment->transaction_id}}</td>
                <td>@money($payment->amount, $invoice->contract->currency, true)</td>
                <td>{{$payment->created_at}}</td>
            @empty
            @endforelse
        </tr>
    </thead>
</table>
