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

use Akaunting\Money\Currency;
use App\Support\Money;
use Illuminate\Support\Str;
use Modules\Core\Facades\Innoclapps;

if (! function_exists('format_bytes')) {
    /**
     * Format the given bytes in a proper human readable format
     *
     * @param  int|float  $bytes
     * @param  int  $precision
     * @return string
     */
    function format_bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}

if (! function_exists('timezone')) {
    /**
     * Helper timezone function
     *
     * @return \Modules\Core\Timezone
     */
    function timezone()
    {
        return app('timezone');
    }
}

if (! function_exists('tz')) {
    /**
     * Alias to timezone() function
     *
     * @return \Modules\Core\Timezone
     */
    function tz()
    {
        return timezone();
    }
}

if (! function_exists('get_generated_lang')) {
    /**
     * Get the application generate language
     *
     * @param  null|string  $locale
     * @return \stdClass
     */
    function get_generated_lang($locale = null)
    {
        $path = config('translator.json');

        if (! is_file($path)) {
            return [];
        }

        $content = json_decode(
            file_get_contents($path)
        );

        if (is_null($locale)) {
            return $content;
        }

        return tap(new stdClass, function ($localeClass) use ($content, $locale) {
            if (isset($content->{$locale})) {
                $localeClass->{$locale} = $content->{$locale};
            } else {
                foreach ([config('app.locale'), config('app.fallback_locale')] as $fallback) {
                    if (isset($content->{$fallback})) {
                        $localeClass->{$fallback} = $content->{$fallback};

                        break;
                    }
                }
            }
        });
    }
}

if (! function_exists('clone_prefix')) {
    /**
     * Add clone prefix to the given string
     *
     * Can be used when cloning models, to generaet unique name/title etc...
     */
    function clone_prefix(string $to): string
    {
        $title = preg_replace('/\s-\sCopy\([a-zA-Z0-9]{6}+\)/', '', $to);

        return $title.' - Copy('.Str::random(6).')';
    }
}

if (! function_exists('clean')) {
    /**
     * Clean the given string
     *
     * @param  string  $dirty
     * @param  mixed  $config
     * @return string
     */
    function clean($dirty, $config = null)
    {
        return app('purifier')->clean($dirty, $config);
    }
}

if (! function_exists('get_current_process_user')) {
    /**
     * Get the current PHP process user.
     *
     * The function returns the process user not the file owner user like get_current_user()
     *
     * @return string
     */
    function get_current_process_user()
    {
        if (! function_exists('posix_getpwuid')) {
            return get_current_user();
        }

        return posix_getpwuid(posix_geteuid())['name'];
    }
}

if (! function_exists('forgot_password_is_disabled')) {
    /**
     * Check if the forgot password auth feature is disabled
     *
     * @return bool
     */
    function forgot_password_is_disabled()
    {
        return settings('disable_password_forgot') === true;
    }
}

if (! function_exists('to_money')) {
    /**
     * Create new Money instance
     *
     * @param  string|int|float  $value
     * @return \Akaunting\Money\Money
     */
    function to_money($value, string|Currency $currency = null)
    {
        if (is_string($currency)) {
            $currency = new Currency($currency);
        } elseif (is_null($currency)) {
            $currency = new Currency(Innoclapps::currency());
        }

        if (! is_float($value)) {
            $value = (float) $value;
        }

        return new Money($value, $currency, true);
    }
}

if (! function_exists('set_alert')) {
    /**
     * Set web alert
     *
     * @param  string|null  $message
     * @param  string  $variant
     * @return void
     */
    function set_alert($message, $variant)
    {
        session()->flash($variant, $message);
    }
}

if (! function_exists('get_current_alert')) {
    /**
     * Get the alert in session
     */
    function get_current_alert(): ?array
    {
        if ($message = session()->get('success')) {
            return ['variant' => 'success', 'message' => $message];
        }
        if ($message = session()->get('error')) {
            return ['variant' => 'danger', 'message' => $message];
        }
        if ($message = session()->get('warning')) {
            return ['variant' => 'warning', 'message' => $message];
        }
        if ($message = session()->get('info')) {
            return ['variant' => 'info', 'message' => $message];
        }

        return null;
    }
}
