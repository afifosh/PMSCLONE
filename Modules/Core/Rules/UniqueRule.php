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

use Illuminate\Validation\Rules\Unique;
use Modules\Core\Makeable;

class UniqueRule extends Unique
{
    use Makeable;

    /**
     * Create a new rule instance.
     */
    public function __construct(string $model, mixed $ignore = null, string|null $column = 'NULL')
    {
        parent::__construct(
            app($model)->getTable(),
            $column
        );

        if (! is_null($ignore)) {
            $ignoredId = is_int($ignore) ? $ignore : (request()->route($ignore) ?: null);

            $this->ignore($ignoredId);
        }
    }
}
