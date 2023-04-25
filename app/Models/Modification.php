<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Approval\Models\Modification as BaseModification;

class Modification extends BaseModification
{
  protected $casts = [
    'modifications' => 'array',
  ];

  public function isApprovable($level)
  {
    return $level > $this->approvals()->count() && !$this->disapprovals()->count();
  }
}
