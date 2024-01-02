@if ($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Pause Contract')
    <span class="badge bg-label-warning">Pause Contract</span>
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Resume')
    <span class="badge bg-label-success">Resume Contract</span>
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Termination')
    <span class="badge bg-label-danger">Terminat Contract</span>
@elseif($changeRequest->type == 'Terms')
    <span class="badge bg-label-warning">Terms Update</span>
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Early Completed')
    <span class="badge bg-label-success">Early Completed</span>
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Completed')
    <span class="badge bg-label-success">Completed</span>
@endif
