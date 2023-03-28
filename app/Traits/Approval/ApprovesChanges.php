<?php

namespace App\Traits\Approval;

use Approval\Traits\ApprovesChanges as BaseApprovesChanges;

trait ApprovesChanges
{
  use BaseApprovesChanges;
    /**
     * Approve a modification.
     *
     * @param \Approval\Models\Modification $modification
     * @param string|null $reason
     *
     * @return bool
     */
    public function approve(\Approval\Models\Modification $modification, ?string $reason = null): bool
    {
        if ($this->authorizedToApprove($modification)) {

            // Prevent disapproving and approving
            if ($disapproval = $this->disapprovals()->where([
                'disapprover_id'   => $this->{$this->primaryKey},
                'disapprover_type' => get_class(),
                'modification_id'  => $modification->id,
            ])->first()) {
                $disapproval->delete();
            }

            $approvalModel = config('approval.models.approval', \Approval\Models\Approval::class);
            $approvalModel::create([
                'approver_id'     => $this->{$this->primaryKey},
                'approver_type'   => get_class(),
                'modification_id' => $modification->id,
                'reason' => $reason
            ]);

            $modification->fresh();

            if ($modification->approversRemaining == 0) {
                if ($modification->modifiable_id === null) {
                    $polymorphicModel = new $modification->modifiable_type();
                    $polymorphicModel->applyModificationChanges($modification, true);
                } else {
                    $modification->modifiable->applyModificationChanges($modification, true);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Disapprove a modification.
     *
     * @param \Approval\Models\Modification $modification
     * @param string|null $reason
     *
     * @return bool
     */
    public function disapprove(\Approval\Models\Modification $modification, ?string $reason = null): bool
    {
        if ($this->authorizedToDisapprove($modification)) {

            // Prevent approving and disapproving
            // if ($approval = $this->approvals()->where([
            //     'approver_id'     => $this->{$this->primaryKey},
            //     'approver_type'   => get_class(),
            //     'modification_id' => $modification->id,
            // ])->first()) {
            //     $approval->delete();
            // }

            // Prevent duplicates
            $disapprovalModel = config('approval.models.disapproval', \Approval\Models\Disapproval::class);
            $disapprovalModel::create([
                'disapprover_id'   => $this->{$this->primaryKey},
                'disapprover_type' => get_class(),
                'modification_id'  => $modification->id,
                'reason' => $reason
            ]);

            $modification->fresh();

            if ($modification->disapproversRemaining == 0) {
                if ($modification->modifiable_id === null) {
                    $polymorphicModel = new $modification->modifiable_type();
                    $polymorphicModel->applyModificationChanges($modification, false);
                } else {
                    $modification->modifiable->applyModificationChanges($modification, false);
                }
            }

            return true;
        }

        return false;
    }
}
