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

use Illuminate\Translation\FileLoader;
use App\Innoclapps\Contracts\Translation\TranslationLoader;

class LoaderManager extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null) : array
    {
        $originalTranslations = parent::load($locale, $group, $namespace);

        if (! is_null($namespace) && $namespace !== '*') {
            return $originalTranslations;
        }

        // JSON translations are not supported ATM
        if ($group === '*') {
            return $originalTranslations;
        }

        $fallbackLocale = config('app.fallback_locale');

        if ($fallbackLocale && $locale != $fallbackLocale) {
            $fallback = parent::load($fallbackLocale, $group, $namespace);
        }

        return array_replace_recursive(
            $fallback ?? [],
            $originalTranslations,
            app(TranslationLoader::class)->loadTranslations($locale, $group)
        );
    }
}
