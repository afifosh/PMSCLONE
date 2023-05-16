<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Database\Factories\ModelVisibilityGroupDependentFactory;

class ModelVisibilityGroupDependent extends Model
{
    use HasFactory;

    /**
     * Indicates if the model has timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the visibility dependent model group
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ModelVisibilityGroup::class, 'model_visibility_group_id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ModelVisibilityGroupDependentFactory
    {
        return ModelVisibilityGroupDependentFactory::new();
    }
}
