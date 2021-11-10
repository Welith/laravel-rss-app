<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

use function response;

/**
 * Class GeneralException.
 */
class GeneralException extends Exception
{
    public $message;

    /**
     * GeneralException constructor.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct($message = '', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function render(Request $request): Response
    {
        return response([
            'status'  => 500,
            'message' => "Internal Service Error"
        ]);
    }
}
