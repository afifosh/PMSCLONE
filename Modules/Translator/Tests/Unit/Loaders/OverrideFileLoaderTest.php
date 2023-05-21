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

namespace Modules\Translator\Tests\Unit\Loaders;

use Illuminate\Support\Facades\File;
use Modules\Translator\Contracts\TranslationLoader;
use Modules\Translator\Translator;
use Tests\TestCase;

class OverrideFileLoaderTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach (['en_TEST', '.custom/en_TEST', '.custom', 'fr_TEST'] as $folder) {
            $path = lang_path($folder);

            if (is_dir($path)) {
                File::deepCleanDirectory($path);
            }
        }

        parent::tearDown();
    }

    public function test_it_can_load_the_overriden_translations()
    {
        File::ensureDirectoryExists(lang_path('en_TEST'));
        File::ensureDirectoryExists(lang_path('fr_TEST'));

        File::copy(base_path('tests/Fixtures/locale_group.php'), lang_path('en_TEST/locale_group.php'));
        File::copy(base_path('tests/Fixtures/locale_group.php'), lang_path('fr_TEST/locale_group.php'));

        $translator = new Translator;

        $translator->save('fr_TEST', 'locale_group', [
            'key' => 'changed',
            'deep' => [
                'key' => 'changed',
            ],
        ]);

        $loader = app(TranslationLoader::class);

        $translations = $loader->loadTranslations('fr_TEST', 'locale_group');

        $this->assertIsArray($translations);
        $this->assertCount(2, $translations);
        $this->assertArrayHasKey('key', $translations);
        $this->assertArrayHasKey('deep', $translations);
        $this->assertSame('changed', $translations['key']);
        $this->assertSame('changed', $translations['deep']['key']);
    }

    public function test_it_can_save_custom_translations()
    {
        File::ensureDirectoryExists(lang_path('en_TEST'));
        File::copy(base_path('tests/Fixtures/locale_group.php'), lang_path('en_TEST/locale_group.php'));

        $translator = new Translator;

        $translator->save('en_TEST', 'locale_group', [
            'key' => 'changed',
            'deep' => [
                'key' => 'changed',
            ],
        ]);

        $this->assertDirectoryExists(lang_path('.custom/en_TEST'));
        $this->assertFileExists(lang_path('.custom/en_TEST/locale_group.php'));
    }

    public function test_locale_has_original_translations()
    {
        File::ensureDirectoryExists(lang_path('en_TEST'));
        File::ensureDirectoryExists(lang_path('fr_TEST'));

        File::copy(base_path('tests/Fixtures/locale_group.php'), lang_path('en_TEST/locale_group.php'));
        File::copy(base_path('tests/Fixtures/locale_group.php'), lang_path('fr_TEST/locale_group.php'));

        $translator = (new Translator)->setFallbackLocale('en_TEST');

        $translator->save('fr_TEST', 'locale_group', [
            'key' => 'changed',
            'deep' => [
                'key' => 'changed',
            ],
        ]);

        $groups = $translator->source('fr_TEST')['groups'];

        $this->assertIsArray($groups);
        $this->assertArrayHasKey('locale_group', $groups);
        $this->assertCount(2, $groups['locale_group']);
        $this->assertArrayHasKey('key', $groups['locale_group']);
        $this->assertSame('value', $groups['locale_group']['key']);
    }
}
