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

namespace Modules\Core\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\File;
use Modules\Core\Facades\MailableTemplates;
use Modules\Core\Models\MailableTemplate;
use Tests\Fixtures\SampleMailTemplate;
use Tests\TestCase;

class MailableTest extends TestCase
{
    public function test_mailable_template_is_seeded_when_new_mailable_exist()
    {
        RefreshDatabaseState::$lazilyRefreshed = true;
        $this->baseRefreshDatabase();

        MailableTemplates::dontDiscover();
        MailableTemplates::flushCache()->register(SampleMailTemplate::class)->seedIfRequired();

        $this->assertDatabaseHas('mailable_templates', [
            'name' => SampleMailTemplate::name(),
            'subject' => SampleMailTemplate::defaultSubject(),
            'html_template' => SampleMailTemplate::defaultHtmlTemplate(),
            'text_template' => SampleMailTemplate::defaultTextMessage(),
            'mailable' => SampleMailTemplate::class,
            'locale' => 'en',
        ]);
    }

    public function test_mailable_templates_are_seeded_when_new_locale_exist()
    {
        RefreshDatabaseState::$lazilyRefreshed = true;
        $this->baseRefreshDatabase();

        File::ensureDirectoryExists(lang_path('en_TEST'));

        MailableTemplates::seedIfRequired();

        $total = count(MailableTemplates::get());
        $this->assertCount($total, MailableTemplate::forLocale('en_TEST')->get());
    }

    public function test_mailable_templates_are_seeded_for_all_locales()
    {
        RefreshDatabaseState::$lazilyRefreshed = true;
        $this->baseRefreshDatabase();

        File::ensureDirectoryExists(lang_path('en_TEST'));
        File::ensureDirectoryExists(lang_path('fr_TEST'));

        MailableTemplates::seedIfRequired();

        $total = count(MailableTemplates::get());
        $this->assertCount($total, MailableTemplate::forLocale('en_TEST')->get());
        $this->assertCount($total, MailableTemplate::forLocale('fr_TEST')->get());
    }

    protected function tearDown(): void
    {
        foreach (['en_TEST', 'fr_TEST'] as $locale) {
            $path = lang_path($locale);

            if (is_dir($path)) {
                File::deepCleanDirectory($path);
            }
        }

        parent::tearDown();
    }
}
