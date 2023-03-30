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

namespace App\Innoclapps\Contracts\Translation;

use App\Innoclapps\Translation\DotNotationResult;

interface TranslationLoader
{
    /**
     * Returns all translations for the given locale and group.
     *
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    public function loadTranslations(string $locale, string $group) : array;

    /**
     * Save the given translations in storage
     *
     * @param string $locale
     * @param string $group
     * @param \App\Innoclapps\Translation\DotNotationResult $translations
     *
     * @return void
     */
    public function saveTranslations(string $locale, string $group, DotNotationResult $translations);

    /**
     * Get the original untouched translations
     *
     * @param string $locale
     *
     * @return \App\Innoclapps\Translation\DotNotationResult
     */
    public function getOriginal(string $locale) : DotNotationResult;
}
