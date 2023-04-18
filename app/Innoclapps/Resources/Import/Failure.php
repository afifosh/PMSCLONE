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

namespace App\Innoclapps\Resources\Import;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;

class Failure implements Arrayable, JsonSerializable
{
    /**
     * Create new Failure instance
     *
     * @param integer $row
     * @param string $attribute
     * @param array $errors
     * @param array $values
     */
    public function __construct(protected int $row, protected string $attribute, protected array $errors, protected array $values = [])
    {
    }

    /**
     * @return int
     */
    public function row() : int
    {
        return $this->row;
    }

    /**
     * @return string
     */
    public function attribute() : string
    {
        return $this->attribute;
    }

    /**
     * @return array
     */
    public function errors() : array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function values() : array
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return collect($this->errors)->map(function ($message) {
            return __('There was an error on row :row. :message', ['row' => $this->row, 'message' => $message]);
        })->all();
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'row'       => $this->row(),
            'attribute' => $this->attribute(),
            'errors'    => $this->errors(),
            'values'    => $this->values(),
        ];
    }
}
