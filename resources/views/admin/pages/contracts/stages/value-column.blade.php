<div class="text-start">
  <div><span class="text-success">Total:</span> @money($stage->stage_amount ?? 0, $stage->contract->currency, true)</div>
  <div><span class="text-success">Remaining:</span> @money($stage->remaining_amount ?? 0, $stage->contract->currency, true)</div>
</div>
