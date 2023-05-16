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

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Core\VisibilityGroup\HasVisibilityGroups;
use Modules\Users\Models\User;

class VisibleModelRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
    public function __construct(protected HasVisibilityGroups&Model $model, protected ?User $user = null)
    {
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if ($this->isNotVisible($value)) {
            $fail('This :attribute value is forbidden.');
        }
    }

    /**
     * Check whether the given model ID is visible to the current user
     */
    protected function isNotVisible(string|int $id): bool
    {
        return $this->model->query()
            ->select($this->model->getKeyName())
            ->where($this->model->getKeyName(), $id)
            ->visible($this->user ?: Auth::user())
            ->count() === 0;
    }
}
