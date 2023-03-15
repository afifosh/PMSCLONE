<?php

namespace App\Traits\Approval;

use App\Models\ApprovalLevel;
use Approval\Traits\RequiresApproval;

trait CompanyApprovalBaseLogic
{
  use RequiresApproval;

  public function __construct()
  {
    parent::__construct();

    $this->approversRequired = ApprovalLevel::count(); //one approver per approval level
  }

  protected function requiresApprovalWhen(array $modifications): bool
  {
    // Handle some logic that determines if this change requires approval
    //
    // Return true if the model requires approval, return false if it
    // should update immediately without approval.
    return true;
  }
}
