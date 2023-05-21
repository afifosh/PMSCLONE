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

class RequirementsChecker
{
    /**
     * Check the installer requirements
     */
    public function check(): array
    {
        $results = $this->createEmptyResultSet();
        $requirements = config('installer.requirements');

        foreach ($requirements as $type => $requirement) {
            switch ($type) {
                case 'php':
                    $checks = $this->checkPHPRequirements($requirements[$type]);

                    $results['results'][$type] = array_merge($results['results'][$type], $checks);

                    if ($this->determineIfFails($checks)) {
                        $results['errors'] = true;
                    }

                    break;

                case 'functions':
                    $checks = $this->checkPHPFunctions($requirements[$type]);

                    $results['results'][$type] = array_merge($results['results'][$type], $checks);

                    if ($this->determineIfFails($checks)) {
                        $results['errors'] = true;
                    }

                    break;

                case 'apache':
                    foreach ($requirements[$type] as $requirement) {
                        // if function doesn't exist we can't check apache modules
                        if (function_exists('apache_get_modules')) {
                            $results['results'][$type][$requirement] = true;

                            if (! in_array($requirement, apache_get_modules())) {
                                $results['results'][$type][$requirement] = false;

                                $results['errors'] = true;
                            }
                        }
                    }

                    break;
                case 'recommended':
                    $results['recommended']['php'] = $this->checkPHPRequirements($requirements[$type]['php']);
                    $results['recommended']['functions'] = $this->checkPHPFunctions($requirements[$type]['functions']);

                    break;
            }
        }

        return $results;
    }

    /**
     * Check whether the given PHP requirement passes
     */
    public function passes(string $requirement): bool
    {
        $requirements = $this->check();

        if (! array_key_exists($requirement, $requirements['recommended']['php'])) {
            return $requirements['results']['php'][$requirement] ?? true;
        }

        return $requirements['recommended']['php'][$requirement];
    }

    /**
     * Check whether the given PHP requirement fails
     */
    public function fails(string $requirement): bool
    {
        return ! $this->passes($requirement);
    }

    /**
     * Check the php requirements
     */
    protected function checkPHPRequirements(array $requirements): array
    {
        $results = [];

        foreach ($requirements as $requirement) {
            $results[$requirement] = extension_loaded($requirement);
        }

        return $results;
    }

    /**
     * Check the PHP functions requirements
     */
    protected function checkPHPFunctions(array $functions): array
    {
        $results = [];

        foreach ($functions as $function) {
            $results[$function] = in_array($function, get_defined_functions()['internal']);
        }

        return $results;
    }

    /**
     * Determine if all checks fails
     */
    protected function determineIfFails(array $checks): bool
    {
        return count(array_filter($checks)) !== count($checks);
    }

    /**
     * Check PHP version requirement.
     */
    public function checkPHPversion(): array
    {
        $minVersionPhp = config('installer.core.minPhpVersion');
        $currentPhpVersion = static::getPhpVersionInfo();
        $supported = version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0;

        return [
            'full' => $currentPhpVersion['full'],
            'current' => $currentPhpVersion['version'],
            'minimum' => $minVersionPhp,
            'supported' => $supported,
        ];
    }

    /**
     * Get current Php version information.
     */
    protected static function getPhpVersionInfo(): array
    {
        $currentVersionFull = PHP_VERSION;
        preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
        $currentVersion = $filtered[0];

        return [
            'full' => $currentVersionFull,
            'version' => $currentVersion,
        ];
    }

    /**
     * Create empty result set
     */
    protected function createEmptyResultSet(): array
    {
        return [
            'results' => [
                'php' => [],
                'functions' => [],
                'apache' => [],
            ],
            'recommended' => [
                'php' => [],
            ],
            'errors' => false,
        ];
    }
}
