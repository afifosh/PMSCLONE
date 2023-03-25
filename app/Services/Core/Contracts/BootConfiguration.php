<?php

namespace App\Services\Core\Contracts;

interface BootConfiguration
{
    /**
     * Responsibe for charging up config
     */
    public function load($configurations);
}
