<?php

namespace App\Enums;

enum WeightUnit: string {
    case KILOGRAM = 'kg';
    case POUND = 'lb';
    // Grams can be added for smaller artworks or detailed weight measurements

    /**
     * Convert enum cases to an associative array for form select options.
     *
     * @return array
     */
    public static function asSelectArray(): array {
        return array_column(self::cases(), 'value', 'name');
    }
        
}
