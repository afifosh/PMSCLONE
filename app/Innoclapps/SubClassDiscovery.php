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

namespace App\Innoclapps;

use ReflectionClass;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class SubClassDiscovery
{
    protected array $directories = [];

    public function __construct(protected string $subclass, null|string|array $directories = null)
    {
        if ($directories) {
            $this->in($directories);
        }
    }

    public function in(string|array $directories) : static
    {
        $this->directories = (array) $directories;

        return $this;
    }

    public function find() : array
    {
        $namespace = app()->getNamespace();
        $classes   = [];

        foreach ((new Finder)->in($this->directories)->files() as $class) {
            $class = $namespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($class->getPathname(), app_path() . DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($class, $this->subclass) && ! (new ReflectionClass($class))->isAbstract()) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}
