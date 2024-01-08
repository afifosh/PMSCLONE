<?php

namespace App\Http\Controllers\Admin\WPForms\Fields;

class WPFormsFieldFactory {
    public static function create($type, $id, $defaults) {
        $className = __NAMESPACE__ . "\\WPForms_Field_" . ucfirst($type);
        if (class_exists($className)) {
            return new $className($id, $defaults);
        }

        throw new \Exception("Invalid field type: " . $type);
    }
}
