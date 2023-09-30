<div class="text-start">Total: @money($stage->stage_amount + $stage->allowable_amount ?? 0, $stage->contract->currency, true)
  <p class="my-0"><span class="text-warning mt-1">Allowable:</span> @money($stage->allowable_amount ?? 0, $stage->contract->currency, true)</p>
  <div><span class="text-success">Remaining:</span> @money($stage->remaining_amount ?? 0, $stage->contract->currency, true)</div>
</div>
