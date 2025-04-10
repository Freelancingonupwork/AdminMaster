<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidArgumentException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
        AccessDeniedHttpException::class,
    ];

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
     * Report or log an exception.
     *
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        return $this->handleException($request, $e);
    }

    public function handleException($request, Throwable $exception): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            if ($request->is('api/*')) {
                return $this->errorResponse('The specified method for the request is invalid', [0 => [
                    'message' => 'Please try with other request type (POST, PUT, GET, DELETE).',
                    'fieldName' => 'API',
                    'errors' => $exception->getMessage(),
                ]], 405);
            }

            if(empty(Auth::user()->roles[0])){
                return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }else{
                return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }
        }

        if ($exception instanceof NotFoundHttpException) {
            if ($request->is('api/*')) {
                return $this->errorResponse('The specified URL cannot be found', [0 => [
                    'message' => 'The API endpoint is invalid.',
                    'fieldName' => 'endpoint',
                    'errors' => $exception->getMessage(),
                ]], 404);
            }

            if(empty(Auth::user()->roles[0])){
                return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }else{
                return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }
        }

        if ($exception instanceof HttpException) {
            if ($request->is('api/*')) {
                return $this->errorResponse('Unauthorized action', [0 => [
                    'message' => 'The authenticated user is not allowed to access the specified API endpoint.',
                    'fieldName' => 'role',
                    'errors' => $exception->getMessage(),
                ]], $exception->getStatusCode());
            }

            if(empty(Auth::user()->roles[0])){
                return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }else{
                return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }
        }
        if ($exception instanceof AuthenticationException) {
            if ($request->is('api/*')) {
                return $this->errorResponse('You are logged out from this system', [0 => [
                    'message' => 'Please re-login to the system.',
                    'fieldName' => 'token',
                    'errors' => $exception->getMessage(),
                ]], 401);
            }

            if(empty(Auth::user()->roles[0])){
                return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }else{
                return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }
        }
        if ($exception instanceof ModelNotFoundException) {
            $trim = explode('/', Request::getPathInfo());

            if ($request->is('api/*')) {
                return $this->errorResponse('No ' . $trim[3] . ' Data Found', [0 => [
                    'message' => 'The ' . $trim[3] . ' is not found',
                    'fieldName' => 'id',
                    'errors' => $exception->getMessage(),
                ]], 404);
            }

            if(empty(Auth::user()->roles[0])){
                return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }else{
                return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }
        }

        if ($exception instanceof ValidationException) {
            $error = [];

            if ($request->is('api/*')) {
                foreach ($exception->errors() as $key => $value) {
                    $error = array_merge($error, $value);
                }

                if(empty(Auth::user()->roles[0])){
                    return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
                }else{
                    return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
                }
            }
        }
        if ($exception instanceof QueryException) {
            if ($request->is('api/*')) {
                return $this->errorResponse('Database Query Error', [0 => [
                    'message' => 'Invalid SQL format or Table field or Type',
                    'fieldName' => 'database',
                    'errors' => $exception->getMessage(),
                ]], 502);
            }
            if(empty(Auth::user()->roles[0])){
                return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }else{
                return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }
        }

        if ($exception instanceof InvalidArgumentException) {
            if ($request->is('api/*')) {
                return $this->errorResponse('Error', $exception->getMessage(), $exception->getCode());
            }

            if(empty(Auth::user()->roles[0])){
                return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }else{
                return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }
        }
        if ($exception instanceof AccessDeniedHttpException) {
            if ($request->is('api/*')) {
                return $this->errorResponse('Error', $exception->getMessage(), $exception->getCode());
            }

            if(empty(Auth::user()->roles[0])){
                return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }else{
                return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
            }
        }
        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        if ($request->is('api/*')) {
            return $this->errorResponse('Unexpected Exception. Try later', $exception->getTrace(), $exception->getCode());
        }

        if(empty(Auth::user()->roles[0])){
            return response()->view('front_error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
        }else{
            return response()->view('error', ['error' => $exception, 'status' => 502, 'message' => "Invalid SQL format or Table field or Type"]);
        }
    }
}
