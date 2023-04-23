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

namespace App\Innoclapps\Contracts\Workflow;

interface FieldChangeTrigger
{
    /**
     * The field to track changes on
     *
     * @return string
     */
    public static function field() : string;

    /**
     * Provide the change field
     *
     * @return \App\Innoclapps\Fields\Field
     */
    public static function changeField();
}
