<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->reportable(function (Exception $ex) {
            return response()->json([
                'code' => '99',
                'message' => 'Unexpected error occured. Try again.',
                'data' => $ex->getMessage(),
            ], 500);
        });

        $this->renderable(function(HttpException $ex, Request $request){
            return response()->json([
                'code' => '99',
                'message' => $ex->getMessage(),
                'data' => $ex->getHeaders()['data'] ?? []
           ], $ex->getStatusCode());
        });

        $this->renderable(function (ValidationException $ex, Request $request) {
            return response()->json([
                'code' => '99',
                'message' => "Fill in appropriate data",
                'data' => $ex->errors()
            ], 400);
        });

        $this->renderable(function (Exception $ex, Request $request) {
            return response()->json([
                'code' => '99',
                'message' => 'Unexpected error occured. Try again.',
                'data' => $ex->getMessage(),
            ], 500);
        });
    }
}
