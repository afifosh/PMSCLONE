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

namespace Modules\Contacts\Database\State;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\Date;
use Modules\Core\Models\Filter;
use Modules\Users\Filters\UserFilter;

class EnsureDefaultFiltersArePresent
{
    public function __invoke()
    {
        foreach (['Contact', 'Company'] as $resource) {
            $this->{'seed'.$resource.'Filters'}();
        }
    }

    public function seedContactFilters()
    {
        if (DB::table('filters')->where('flag', 'my-contacts')->count() === 0) {
            $this->newModelInstance([
                'identifier' => 'contacts',
                'name' => 'contacts::contact.filters.my',
                'flag' => 'my-contacts',
                'rules' => [
                    UserFilter::make()->setOperator('equal')->setValue('me')->toArray(),
                ],
            ])->save();
        }

        if (DB::table('filters')->where('flag', 'my-recently-assigned-contacts')->count() === 0) {
            $this->newModelInstance([
                'identifier' => 'contacts',
                'name' => 'contacts::contact.filters.my_recently_assigned',
                'flag' => 'my-recently-assigned-contacts',
                'rules' => [
                    UserFilter::make()->setOperator('equal')->setValue('me')->toArray(),
                    Date::make('owner_assigned_date')->setOperator('is')->setValue('this_month')->toArray(),
                ],
            ])->save();
        }
    }

    public function seedCompanyFilters()
    {
        if (DB::table('filters')->where('flag', 'my-companies')->count() === 0) {
            $this->newModelInstance([
                'identifier' => 'companies',
                'name' => 'contacts::company.filters.my',
                'flag' => 'my-companies',
                'rules' => [
                    UserFilter::make()->setOperator('equal')->setValue('me')->toArray(),
                ],
            ])->save();
        }

        if (DB::table('filters')->where('flag', 'my-recently-assigned-companies')->count() === 0) {
            $this->newModelInstance([
                'identifier' => 'companies',
                'name' => 'contacts::company.filters.my_recently_assigned',
                'flag' => 'my-recently-assigned-companies',
                'rules' => [
                    UserFilter::make()->setOperator('equal')->setValue('me')->toArray(),
                    Date::make('owner_assigned_date')->setOperator('is')->setValue('this_month')->toArray(),
                ],
            ])->save();
        }
    }

    protected function newModelInstance($attributes)
    {
        return new Filter(array_merge([
            'is_shared' => true,
            'is_readonly' => true,
        ], $attributes));
    }
}
