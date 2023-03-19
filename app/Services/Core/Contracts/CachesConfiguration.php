<?php

namespace App\Services\Core\Contracts;

interface CachesConfiguration
{
    public function load();

    public static function handle();

    public static function update(string $cacheKey, string $provider, string $service): bool;
}
