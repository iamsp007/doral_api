<?php

namespace App\Exceptions;

use App\Helpers\Helper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function render($request, \Throwable $exception)
    {
        $helper = new Helper();
        // This will replace our 404 response with
        // a JSON response.
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {

            return $helper->generateResponse(false,$exception->getMessage(),null,403);
        }
        if ($exception instanceof UnauthorizedException){

            return $helper->generateResponse(false,$exception->getMessage(),null,401);
        }

        return parent::render($request, $exception);
    }
}
