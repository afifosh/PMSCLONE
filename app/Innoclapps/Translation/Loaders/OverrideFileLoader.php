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

namespace App\Innoclapps\Translation\Loaders;

use Illuminate\Support\Facades\File;
use App\Innoclapps\Translation\Translation;
use App\Innoclapps\Translation\DotNotationResult;
use App\Innoclapps\Contracts\Translation\TranslationLoader;

class OverrideFileLoader implements TranslationLoader
{
    /**
     * The custom translator override path
     *
     * @var string
     */
    protected string $overridePath;

    /**
     * Create new OverrideFileLoader instance
     *
     * @param array $config
     */
    public function __construct(protected array $config)
    {
        $this->overridePath = $config['path'];
    }

    /**
     * Returns all translations for the given locale and group.
     *
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    public function loadTranslations(string $locale, string $group) : array
    {
        $groupPath = $this->overridePath . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $group . '.php';

        if (file_exists($groupPath)) {
            $translations = include($groupPath);
        }

        return $translations ?? [];
    }

    /**
     * Save the given translations in storage
     *
     * @param string $locale
     * @param string $group
     * @param \App\Innoclapps\Translation\DotNotationResult $translations
     *
     * @return void
     */
    public function saveTranslations(string $locale, string $group, DotNotationResult $translations)
    {
        File::ensureDirectoryExists(
            $localePath = $this->overridePath . DIRECTORY_SEPARATOR . $locale
        );

        File::put(
            $localePath . DIRECTORY_SEPARATOR . $group . '.php',
            "<?php\n\nreturn " . var_export($translations->clean(), true) . ';' . \PHP_EOL
        );

        Translation::generateJsonLanguageFile();
    }

    /**
     * Get the original unmodified translations using a custom file
     *
     * @param string $locale
     *
     * @return \App\Innoclapps\Translation\DotNotationResult
     */
    public function getOriginal(string $locale) : DotNotationResult
    {
        $fallbackLocale = $this->config['fallback_locale'];

        if (! is_dir($this->config['lang_path'] . DIRECTORY_SEPARATOR . $locale)) {
            $locale = $fallbackLocale;
        }

        $files = File::files($this->config['lang_path'] . DIRECTORY_SEPARATOR . $locale);

        $translations = collect($files)->mapWithKeys(function ($fileInfo) {
            return [$fileInfo->getFilenameWithoutExtension() => include $fileInfo->getPathname()];
        })->when($fallbackLocale && $locale != $fallbackLocale, function ($collection) use ($fallbackLocale) {
            return $this->mergeMissingOriginalKeys($collection, $fallbackLocale);
        })->all();

        return new DotNotationResult(
            $this->mergeMissingOriginalGroups($translations, $fallbackLocale, $locale)
        );
    }

    /**
     * Merge any missing original keys
     *
     * @param \Illuminate\Support\Collection $collection
     * @param string $fallbackLocale
     *
     * @return \Illuminate\Support\Collection
     */
    protected function mergeMissingOriginalKeys($collection, $fallbackLocale)
    {
        // We will merge any missing keys that are added in the fallback locale
        // but they do not exists in the locale we are retrieving the original translations
        // e.q. user create new locale
        // in new version we add new key in 'fields.test' path, this key won't exists in the user locale
        // we need to merge it becuase json generator will be unable to generate translations
        return $collection->mapWithKeys(function ($translations, $group) use ($fallbackLocale) {
            $fallbackPath = $this->config['lang_path'] . DIRECTORY_SEPARATOR . $fallbackLocale . DIRECTORY_SEPARATOR . $group . '.php';

            if (file_exists($fallbackPath)) {
                $translations = array_replace_recursive(include $fallbackPath, $translations);
            }

            return [$group => $translations];
        });
    }

    /**
     * Merge missing groups original translations
     *
     * @param array $translations
     * @param string $fallbackLocale
     * @param string $currentLocale
     *
     * @return array
     */
    protected function mergeMissingOriginalGroups($translations, $fallbackLocale, $currentLocale) : array
    {
        if (! $fallbackLocale) {
            return $translations;
        }

        $missingGroups = Translation::getMissingGroupsForLocale($currentLocale);

        foreach ($missingGroups as $group) {
            $path = $this->config['lang_path'] . DIRECTORY_SEPARATOR . $fallbackLocale . DIRECTORY_SEPARATOR . $group . '.php';

            $translations[$group] = include($path);
        }

        return $translations;
    }
}
