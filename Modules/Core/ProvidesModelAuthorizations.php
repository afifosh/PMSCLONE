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

namespace Modules\Core;

use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use ReflectionMethod;

trait ProvidesModelAuthorizations
{
    /**
     * Get all defined authorizations for the model
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $without Exclude abilities from authorization
     * @return array|null
     */
    public function getAuthorizations($model, $without = [])
    {
        if ($policy = policy($model)) {
            return collect((new ReflectionClass($policy))->getMethods(ReflectionMethod::IS_PUBLIC))
                ->reject(function ($method) use ($without) {
                    return in_array($method->name, array_merge($without, ['denyAsNotFound', 'denyWithStatus', 'before']));
                })
                ->mapWithKeys(fn ($method) => [$method->name => Gate::allows($method->name, $model)])->all();
        }

        return null;
    }
}
