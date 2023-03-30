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

namespace App\Innoclapps\Translation;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class Translation
{
    /**
     * Generate JSON language file
     *
     * @return void
     */
    public static function generateJsonLanguageFile() : void
    {
        (new JsonGenerator())->generateTo(
            config('innoclapps.lang.json')
        );
    }

    /**
     * Get the available locales
     *
     * @return array
     */
    public static function availableLocales() : array
    {
        return once(function () {
            return collect(File::directories(App::langPath()))
                ->map(fn ($locale)    => basename($locale))
                ->reject(fn ($locale) => $locale === 'vendor')
                ->unique()
                ->values()->all();
        });
    }

    /**
     * Check whether the given locale exist
     *
     * @param string $locale
     *
     * @return boolean
     */
    public static function localeExist(string $locale) : bool
    {
        return in_array($locale, static::availableLocales());
    }

    /**
     * Create new locale
     *
     * @param string $name
     *
     * @return boolean
     */
    public static function createNewLocale(string $name) : bool
    {
        $sourceDir = App::langPath(config('app.fallback_locale'));
        $targetDir = App::langPath($name);

        return tap(File::copyDirectory($sourceDir, $targetDir), function () {
            // availableLocales is using the once() function
            \Spatie\Once\Cache::getInstance()->flush();
            static::generateJsonLanguageFile();
        });
    }

    /**
     * Retrieve the given locale groups files
     *
     * @param string $locale
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public static function retrieveGroupFiles(string $locale)
    {
        $path = App::langPath() . DIRECTORY_SEPARATOR . $locale;

        return is_dir($path) ? File::files($path) : [];
    }

    /**
     * Get the locale available groups
     *
     * @param string $locale
     *
     * @return array
     */
    public static function getGroups(string $locale) : array
    {
        return array_map(
            fn ($file) => $file->getFilenameWithoutExtension(),
            static::retrieveGroupFiles($locale)
        );
    }

    /**
     * Get the given locale groups translations
     *
     * @param string $locale
     *
     * @return array
     */
    public static function getGroupsTranslations(string $locale) : array
    {
        $original = App::getLocale();

        try {
            App::setLocale($locale);

            // We will be using laravel trans helper because if the group does not exists
            // Laravel automatically fallback to the fallback locale, in this case, en
            $translations = collect(static::getGroups($locale))
                ->merge(static::getMissingGroupsForLocale($locale))
                ->mapWithKeys(fn ($group) => [$group => trans($group)])
                ->all();
        } finally {
            App::setLocale($original);
        }

        return $translations;
    }

    /**
     * Get the missing group names for the given locale
     *
     * @param string $locale
     *
     * @return array
     */
    public static function getMissingGroupsForLocale(string $locale) : array
    {
        $fallbackLocale = config('app.fallback_locale');

        if (! $fallbackLocale) {
            return [];
        }

        return array_diff(static::getGroups($fallbackLocale), static::getGroups($locale));
    }

    /**
     * Get the current original translations for a given locale
     *
     * @param string $locale
     *
     * @return \App\Innoclapps\Translation\DotNotationResult
     */
    public static function current(string $locale) : DotNotationResult
    {
        return new DotNotationResult(static::getGroupsTranslations($locale));
    }
}
