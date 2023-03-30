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

use Exception;
use JsonException;

class JsonGenerator
{
    const VUEX_I18N = 'vuex-i18n';

    const VUE_I18N = 'vue-i18n';

    const ESCAPE_CHAR = '!';

    /**
     * The constructor
     *
     * @param array $config
     */
    public function __construct(protected array $config = [])
    {
        if (! isset($this->config['i18nLib'])) {
            $this->config['i18nLib'] = self::VUE_I18N;
        }

        if (! isset($this->config['escape_char'])) {
            $this->config['escape_char'] = self::ESCAPE_CHAR;
        }
    }

    /**
     * Generate and save the file to a given location
     *
     * @param string $path
     *
     * @return integer|boolean
     */
    public function generateTo(string $path) : int|bool
    {
        return file_put_contents($path, $this->generate());
    }

    /**
     * Generate languages JSON file
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generate() : string
    {
        $locales = [];

        foreach (Translation::availableLocales() as $locale) {
            foreach (Translation::getGroupsTranslations($locale) as $groupName => $translations) {
                $locales[$locale][$groupName] = $this->adjustArray($translations);
            }
        }

        try {
            $jsonLocales = json_encode(
                $locales,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
            ) . PHP_EOL;
        } catch (JsonException $e) {
            throw new Exception('Could not generate JSON, error code ' . $e->getMessage());
        }

        return $jsonLocales;
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    protected function adjustArray(array $arr) : array
    {
        $res = [];
        foreach ($arr as $key => $val) {
            $key = $this->removeEscapeCharacter($this->adjustString($key));

            if (is_array($val)) {
                $res[$key] = $this->adjustArray($val);
            } else {
                $res[$key] = $this->removeEscapeCharacter($this->adjustString($val));
            }
        }

        return $res;
    }

    /**
     * Turn Laravel style ":link" into vue-i18n style "{link}" or vuex-i18n style ":::".
     *
     * @param string $str
     *
     * @return string
     */
    protected function adjustString($str)
    {
        if (! is_string($str)) {
            return $str;
        }

        if ($this->config['i18nLib'] === self::VUEX_I18N) {
            $searchPipePattern = '/(\s)*(\|)(\s)*/';
            $threeColons       = ' ::: ';
            $str               = preg_replace($searchPipePattern, $threeColons, $str);
        }

        $str = str_replace('@', '{\'@\'}', $str);

        $escaped_escape_char = preg_quote($this->config['escape_char'], '/');

        return preg_replace_callback(
            "/(?<!mailto|tel|{$escaped_escape_char}):\w+/",
            function ($matches) {
                return '{' . mb_substr($matches[0], 1) . '}';
            },
            $str
        );
    }

    /**
     * Removes escape character if translation string contains sequence that looks like
     *
     * Laravel style ":link", but should not be interpreted as such and was therefore escaped.
     *
     * @param string $str
     *
     * @return string
     */
    protected function removeEscapeCharacter(string $str) : string
    {
        $escaped_escape_char = preg_quote($this->config['escape_char'], '/');

        return preg_replace_callback(
            "/{$escaped_escape_char}(:\w+)/",
            function ($matches) {
                return mb_substr($matches[0], 1);
            },
            $str
        );
    }
}
