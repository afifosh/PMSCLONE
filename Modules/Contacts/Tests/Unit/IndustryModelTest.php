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

namespace Modules\Contacts\Tests\Unit;

use Modules\Contacts\Models\Company;
use Modules\Contacts\Models\Industry;
use Tests\TestCase;

class IndustryModelTest extends TestCase
{
    public function test_industry_has_companies()
    {
        $industry = Industry::factory()->has(Company::factory()->count(2))->create();

        $this->assertCount(2, $industry->companies);
    }

    public function test_industry_with_companies_cannot_be_deleted()
    {
        $industry = Industry::factory()->has(Company::factory())->create();

        $this->expectExceptionMessage(__(
            'core::resource.associated_delete_warning',
            [
                'resource' => __('contacts::company.industry.industry'),
            ]
        ));

        $industry->delete();
    }
}
