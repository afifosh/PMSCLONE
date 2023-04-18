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

namespace App\Innoclapps\Rules;

class UniqueResourceRule extends UniqueRule
{
    /**
     * Indicates whether to exclude the unique validation from import
     *
     * @var boolean
     */
    public $skipOnImport = false;

    /**
     * Create a new rule instance.
     *
     * @param string $modelName
     * @param string|int|null $ignore
     * @param string $column
     *
     * @return void
     */
    public function __construct($modelName, $ignore = 'resourceId', $column = 'NULL')
    {
        parent::__construct($modelName, $ignore, $column);
    }

    /**
     * Set whether the exclude this validation rule from import
     *
     * @param boolean $value
     *
     * @return static
     */
    public function skipOnImport($value)
    {
        $this->skipOnImport = $value;

        return $this;
    }
}
