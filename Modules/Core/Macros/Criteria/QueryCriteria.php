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

namespace Modules\Core\Macros\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Contracts\Criteria\QueryCriteria as QueryCriteriaContract;

class QueryCriteria
{
    /**
     * Register the macor to the builder instance.
     */
    public static function register(): void
    {
        Builder::macro('criteria', function ($criteria) {
            if ($criteria instanceof QueryCriteriaContract || is_string($criteria)) {
                $criteria = [$criteria];
            }

            if (is_iterable($criteria)) {
                foreach ($criteria as $instance) {
                    if (is_string($instance)) {
                        $instance = new $instance;
                    }

                    $instance->apply($this);
                }
            }

            return $this;
        });
    }
}
