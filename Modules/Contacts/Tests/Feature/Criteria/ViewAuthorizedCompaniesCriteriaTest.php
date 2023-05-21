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

namespace Modules\Contacts\Tests\Feature\Criteria;

use Modules\Contacts\Criteria\ViewAuthorizedCompaniesCriteria;
use Modules\Contacts\Models\Company;
use Modules\Core\Database\Seeders\PermissionsSeeder;
use Tests\TestCase;

class ViewAuthorizedCompaniesCriteriaTest extends TestCase
{
    public function test_own_companies_criteria_queries_only_own_companies()
    {
        $this->seed(PermissionsSeeder::class);
        $user = $this->asRegularUser()->withPermissionsTo('view own companies')->createUser();

        Company::factory()->for($user)->create();
        Company::factory()->create();

        $this->signIn($user);

        $query = Company::criteria(ViewAuthorizedCompaniesCriteria::class);

        $this->assertSame(1, $query->count());
    }

    public function test_it_returns_all_companies_when_user_is_authorized_to_see_all_companies()
    {
        $this->seed(PermissionsSeeder::class);
        $user = $this->asRegularUser()->withPermissionsTo('view all companies')->createUser();

        Company::factory()->for($user)->create();
        Company::factory()->create();

        $this->signIn($user);

        $query = Company::criteria(ViewAuthorizedCompaniesCriteria::class);

        $this->assertSame(2, $query->count());

        $this->signIn();
        $this->assertSame(2, $query->count());
    }
}
