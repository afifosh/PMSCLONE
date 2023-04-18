<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps\Rules;

use App\Innoclapps\Makeable;
use Illuminate\Validation\Rules\Unique;

class UniqueRule extends Unique
{
    use Makeable;

    /**
     * Create a new rule instance.
     *
     * @param string $modelName
     * @param string|int|null $ignore
     * @param string $column
     *
     * @return void
     */
    public function __construct($modelName, $ignore = null, $column = 'NULL')
    {
        parent::__construct(
            app($modelName)->getTable(),
            $column
        );

        if (! is_null($ignore)) {
            $ignoredId = is_int($ignore) ? $ignore : (request()->route($ignore) ?: null);

            $this->ignore($ignoredId);
        }
    }
}
