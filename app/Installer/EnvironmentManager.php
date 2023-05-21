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

namespace App\Installer;

class EnvironmentManager
{
    protected string $envPath;

    /**
     * Initialize new EnvironmentManager instance.
     */
    public function __construct(?string $envPath = null)
    {
        $this->envPath = $envPath ?? base_path('.env');
    }

    /**
     * Save the form content to the .env file.
     */
    public function saveEnvFile(Environment $env): bool
    {
        $contents = '# Read more about editing the environment file: https://www.concordcrm.com/docs/config#edit-env-file'."\n\n";

        $contents .= 'APP_NAME=\''.$env->getName()."'\n".
        '# DO NOT EDIT THE APPLICATION KEY'."\n".
        'APP_KEY='.$env->getKey()."\n".
        'IDENTIFICATION_KEY='.$env->getIdentificationKey()."\n".
        'APP_URL='.$env->getUrl()."\n".
        '#APP_DEBUG=true'."\n\n".
        'DB_CONNECTION=mysql'."\n".
        'DB_HOST='.$env->getDbHost()."\n".
        'DB_PORT='.$env->getDbPort()."\n".
        'DB_DATABASE='.$env->getDbName()."\n".
        'DB_USERNAME='.$env->getDbUser()."\n".
        'DB_PASSWORD=\''.$env->getDbPassword()."'\n\n".
        'MODULE_CACHE_ENABLED=true'."\n".
        'MAIL_MAILER=array'."\n";

        $additional = $env->getAdditional();

        if (count($additional) > 0) {
            $contents .= "\n";

            foreach ($additional as $key => $value) {
                $contents .= $key.'='.$value."\n";
            }
        }

        try {
            file_put_contents($this->envPath, $contents);
        } catch (\Exception) {
            return false;
        }

        return true;
    }

    /**
     * Get the memory limit in MB
     *
     * @return int
     */
    public static function getMemoryLimitInMegabytes()
    {
        return \DetachedHelper::getMemoryLimitInMegabytes();
    }

    /**
     * Guess the application URL
     */
    public static function guessUrl(): string
    {
        $guessedUrl = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
        $guessedUrl .= '://'.$_SERVER['HTTP_HOST'];
        $guessedUrl .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        $guessedUrl = preg_replace('/install.*/', '', $guessedUrl);

        return rtrim($guessedUrl, '/');
    }
}
