
{{-- Assuming $status is passed to this partial view --}}
<span class="review-status-class">
  <ul>
    @foreach($status as $stage)
        <li>
            <div>Stage Name: {{ $stage['stage_name'] }}</div>
            <div>Status: {{ $stage['status'] }}</div>
            @if ($stage['last_review_date'])
                <div>Last Review Date: {{ \Carbon\Carbon::parse($stage['last_review_date'])->format('Y-m-d H:i:s') }}</div>
            @endif
        </li>
    @endforeach
</ul>
</span>