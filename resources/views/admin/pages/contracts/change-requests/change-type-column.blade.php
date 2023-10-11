@if ($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Pause Contract')
    <span class="badge bg-label-warning">Pause Contract</span>
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Resume')
    <span class="badge bg-label-success">Resume Contract</span>
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Termination')
    <span class="badge bg-label-danger">Terminat Contract</span>
@elseif($changeRequest->type == 'Terms')
    <span class="badge bg-label-warning">Terms Update</span>
@endif
