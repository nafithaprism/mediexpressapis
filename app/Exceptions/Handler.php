<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        //not found exception controll

        $this->renderable(function (Exception $e, $request) {
           $class = class_basename($e);
            dd($class);
            if ($request->is('v1/api/*')) {
                return response()->json(['ex_message' => 'Record not found.','type' => 'Exception'], 404);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
           $class = class_basename($e);
            if ($request->is('v1/api/*')) {
                return response()->json(['ex_message' => 'Record not found.','type' => 'NotFoundHttpException'], 404);
            }
        });

       // route not found exception controll
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('v1/api/*')) {
                return response()->json(['ex_message' => 'Invalid Route or method.' ,'type' => 'MethodNotAllowedHttpException'], 404);
            }
        });

               // route not found exception controll
        $this->renderable(function (BadMethodCallException $e, $request) {
            if ($request->is('v1/api/*')) {
                return response()->json(['ex_message' => 'Bad Request.' ,'type' => 'BadMethodCallException'], 404);
            }
        });



        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('v1/api/*')) {
                return response()->json(['ex_message' => 'Model not found.' ,'type' => 'ModelNotFoundException' , 'line' =>$e->getLine() ], 404);
            }
        });







    }

    // public function report(Throwable $exception)
    // {
    //     if (app()->bound('sentry') && $this->shouldReport($exception)) {
    //         app('sentry')->captureException($exception);
    //     }

    //     parent::report($exception);
    // }


    // public function register(Exception $exception)
    // {
    //     switch(class_basename($exception)){
    //         case 'TokenMismatchException':

    //             return response()->json(['error' => 66, 'errors' => ['forms' => 'Your request was denied. Please try again or reload your page']], 403);

    //         break;
    //         case 'ThrottleRequestsException':
    //             return response()->json(['errors' => ['forms' => 'You have been rate limited, please try again shortly']], 429);
    //         break;
    //         case 'MethodNotAllowedHttpException':

    //                 return response()->json(['errors' => ['forms' => 'Method Not Allowed']],405);

    //         break;
    //         case 'NotFoundHttpException':

    //                 return response()->json(['errors' => ['forms' => 'We could not locate the data you requested, it may have been lost forever']],404);

    //         break;
    //         case 'MaintenanceModeException':

    //                 return response()->json(['errors' => ['forms' => 'The site is currently down for maintenance, please check back with us soon']],503);

    //         break;
    //         case 'AuthenticationException':
    //         case 'ValidationException':
    //             return parent::render($request, $exception);
    //         break;
    //     }
    //     if (app()->isProduction()){
    //         if ($request->expectsJson()){
    //             return response()->json('Server Error',500);
    //         }
    //         return response()->view('errors.500', [], 500);
    //     }
    //     return parent::render($request, $exception);
    // }

}
