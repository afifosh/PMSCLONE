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

namespace App\Innoclapps\Export\Exceptions;

use Exception;

class InvalidExportTypeException extends Exception
{
    /**
     * Create new InvalidExportTypeException instnace.
     *
     * @param string $type
     * @param integer $code
     * @param \Exception|null $previous
     */
    public function __construct($type, $code = 0, Exception $previous = null)
    {
        parent::__construct("The export type \"$type\" is not supported.", $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(['message' => $this->getMessage()], $this->getCode() ?: 500);
    }
}
