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

use Modules\Activities\Models\Activity;
use Modules\Calls\Models\Call;
use Modules\Contacts\Models\Company;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Phone;
use Modules\Contacts\Models\Source;
use Modules\Core\Database\Seeders\CountriesSeeder;
use Modules\Core\Models\Country;
use Modules\Deals\Models\Deal;
use Modules\Notes\Models\Note;
use Modules\Users\Models\User;
use Tests\TestCase;

class CompanyModelTest extends TestCase
{
    public function test_when_company_created_by_not_provided_uses_current_user_id()
    {
        $user = $this->signIn();

        $company = Company::factory(['created_by' => null])->create();

        $this->assertEquals($company->created_by, $user->id);
    }

    public function test_company_created_by_can_be_provided()
    {
        $user = $this->createUser();

        $company = Company::factory()->for($user, 'creator')->create();

        $this->assertEquals($company->created_by, $user->id);
    }

    public function test_company_has_path_attribute()
    {
        $company = Company::factory()->create();

        $this->assertEquals('/companies/1', $company->path);
    }

    public function test_company_has_display_name_attribute()
    {
        $company = Company::factory(['name' => 'Company name'])->make();

        $this->assertEquals('Company name', $company->display_name);
    }

    public function test_company_has_country()
    {
        $this->seed(CountriesSeeder::class);

        $company = Company::factory()->for(Country::first())->create();

        $this->assertInstanceOf(Country::class, $company->country);
    }

    public function test_company_has_user()
    {
        $company = Company::factory()->for(User::factory())->create();

        $this->assertInstanceOf(User::class, $company->user);
    }

    public function test_company_has_source()
    {
        $company = Company::factory()->for(Source::factory())->create();

        $this->assertInstanceOf(Source::class, $company->source);
    }

    public function test_company_has_deals()
    {
        $company = Company::factory()->has(Deal::factory()->count(2))->create();

        $this->assertCount(2, $company->deals);
    }

    public function test_company_has_phones()
    {
        $this->seed(CountriesSeeder::class);

        $company = Company::factory()->has(Phone::factory()->count(2))->create();

        $this->assertCount(2, $company->phones);
    }

    public function test_company_has_calls()
    {
        $company = Company::factory()->has(Call::factory()->count(2))->create();

        $this->assertCount(2, $company->calls);
    }

    public function test_company_has_notes()
    {
        $company = Company::factory()->has(Note::factory()->count(2))->create();

        $this->assertCount(2, $company->notes);
    }

    public function test_company_has_contacts()
    {
        $company = Company::factory()->has(Contact::factory()->count(2))->create();

        $this->assertCount(2, $company->contacts);
    }

    public function test_company_has_activities()
    {
        $company = Company::factory()->has(Activity::factory()->count(2))->create();

        $this->assertCount(2, $company->activities);
    }
}
