<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLevelApprover extends Model
{
    use HasFactory;

    public function level()
    {
        return $this->belongsTo(ApprovalLevel::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class);
    }

    public function scopeForLevel($query, $level)
    {
        return $query->where('workflow_level_id', $level->id);
    }

    public function scopeForApprover($query, $approver)
    {
        return $query->where('user_id', $approver->id);
    }
}
