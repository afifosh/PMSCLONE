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
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Source;
use Tests\TestCase;

class SourceModelTest extends TestCase
{
    public function test_source_can_be_primary()
    {
        $source = Source::factory()->primary()->create();
        $this->assertTrue($source->isPrimary());

        $source->flag = null;
        $source->save();

        $this->assertFalse($source->isPrimary());
    }

    public function test_source_has_contacts()
    {
        $source = Source::factory()->has(Contact::factory()->count(2))->create();

        $this->assertCount(2, $source->contacts);
    }

    public function test_source_has_companies()
    {
        $source = Source::factory()->has(Company::factory()->count(2))->create();

        $this->assertCount(2, $source->companies);
    }

    public function test_source_has_by_flag_scope()
    {
        $source = Source::factory()->create(['flag' => 'custom-flag']);

        $byFlag = Source::findByFlag('custom-flag');

        $this->assertInstanceOf(Source::class, $byFlag);
        $this->assertEquals($source->id, $byFlag->id);
    }

    public function test_primary_source_cannot_be_deleted()
    {
        $source = Source::factory()->primary()->create();
        $this->expectExceptionMessage(__('contacts::source.delete_primary_warning'));

        $source->delete();
    }

    public function test_source_with_contacts_cannot_be_deleted()
    {
        $source = Source::factory()->has(Contact::factory()->for($this->createUser()))->create();

        $this->expectExceptionMessage(__(
            'core::resource.associated_delete_warning',
            [
                'resource' => __('contacts::source.source'),
            ]
        ));

        $source->delete();
    }

    public function test_source_with_companies_cannot_be_deleted()
    {
        $source = Source::factory()->has(Company::factory())->create();

        $this->expectExceptionMessage(__(
            'core::resource.associated_delete_warning',
            [
                'resource' => __('contacts::source.source'),
            ]
        ));

        $source->delete();
    }
}
