<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Approval\Models\Modification as BaseModification;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modification extends BaseModification
{
  use SoftDeletes;

  protected $casts = [
    'modifications' => 'array',
  ];

  public function isApprovable($level)
  {
    return $level > $this->approvals()->count() && !$this->disapprovals()->count();
  }

  public function updateModifications($modifications)
  {
    $this->mergeModifications($modifications);
    $this->md5 = md5(json_encode($this->modifications));
    $this->save();
  }

  public function mergeModifications($modifications)
  {
    // merge $modification[key][modified] with $modifications[key]
    $mergedModifications = $this->modifications;

    foreach ($this->getModelFillables() as $fileable) {
      $mergedModifications[$fileable]['modified'] = @$modifications[$fileable] ? @$modifications[$fileable] : $mergedModifications[$fileable]['modified'] ?? null;
      $mergedModifications[$fileable]['original'] = @$mergedModifications[$fileable]['original'] ?? null;
    }

    $this->modifications = $mergedModifications;
  }

  public function getModelFillables()
  {
    $model = new $this->modifiable_type();
    return $model->getFillables();
  }
}
