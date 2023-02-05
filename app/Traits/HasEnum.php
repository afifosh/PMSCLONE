<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasEnum
{
  public static function getPossibleEnumValues($column)
  {
    // Create an instance of the model to be able to get the table name
    $instance = new static;

    $arr = DB::select(DB::raw('SHOW COLUMNS FROM ' . $instance->getTable() . ' WHERE Field = "' . $column . '"'));
    if (count($arr) == 0) {
      return array();
    }
    // Pulls column string from DB
    $enumStr = $arr[0]->Type;

    // Parse string
    preg_match_all("/'([^']+)'/", $enumStr, $matches);

    // Return matches
    return isset($matches[1]) ? $matches[1] : [];
  }
}
