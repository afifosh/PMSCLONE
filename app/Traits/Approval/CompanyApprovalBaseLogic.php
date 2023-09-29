<?php

namespace App\Traits\Approval;

use App\Models\ApprovalLevel;
use Approval\Traits\RequiresApproval;

trait CompanyApprovalBaseLogic
{
  use RequiresApproval;

  protected static $approversCount;

  public function __construct()
  {
    parent::__construct();

    if (!isset(static::$approversCount)) {
      static::$approversCount = ApprovalLevel::count();
    }

    $this->approversRequired = static::$approversCount; //one approver per approval level
  }

  protected function requiresApprovalWhen(array $modifications): bool
  {
    return @$modifications['doc_requestable_type'] !=  'App\Models\Contract';
    // Handle some logic that determines if this change requires approval
    //
    // Return true if the model requires approval, return false if it
    // should update immediately without approval.
    // return true;
  }

  /**
   * Apply modification to model.
   *
   * @return void
   */
  public function applyModificationChanges(\Approval\Models\Modification $modification, bool $approved)
  {
    if ($approved && $this->updateWhenApproved) {
      $this->setForcedApprovalUpdate(true);

      foreach ($modification->modifications as $key => $mod) {
        $this->{$key} = $mod['modified'];
      }

      $this->save();

      // update modification to reflect the model that was updated
      $modification->modifiable_type = get_class($this);
      $modification->modifiable_id = $this->id;
      $modification->save();

      if ($this->deleteWhenApproved) {
        $modification->delete();
      } else {
        $modification->active = false;
        $modification->save();
      }
    } elseif ($approved === false) {
      if ($this->deleteWhenDisapproved) {
        $modification->delete();
      } else {
        $modification->active = false;
        $modification->save();
      }
    }
  }

  /**
   * Get fillable Fields.
   *
   * @return array
   */
  public function getFillables()
  {
    return $this->fillable;
  }
}
