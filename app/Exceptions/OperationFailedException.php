<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class OperationFailedException extends Exception
{
    public $data;

    /**
     * OperationFailedException constructor.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  Exception  $previous
     * @param $data
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, $data = null)
    {
        if ($code == 0) {
            $code = Response::HTTP_BAD_REQUEST;
        }
        parent::__construct($message, $code, $previous);
        $this->data = $data;
        return $data;
    }
}