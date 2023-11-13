<?php

namespace App\Enums;

enum LengthUnit: string {
    case CENTIMETER = 'cm';
    case METER = 'm';
    case INCH = 'inch';
    // Add more units as needed

    /**
     * Convert enum cases to an associative array for form select options.
     *
     * @return array
     */
    public static function asSelectArray(): array {
        return array_column(self::cases(), 'value', 'name');
    }
        
}
