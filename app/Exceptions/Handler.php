<?php

namespace App\Exceptions;

use App\Traits\JsonResponder;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return response()->json([
                'responseMessage' => 'You do not have the required authorization.',
                'responseStatus' => 403,
            ]);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $exceptionClass = get_class($exception);
        $message = $exception->getMessage();

        switch ($exceptionClass) {
            case "Symfony\Component\HttpKernel\Exception\NotFoundHttpException":
                return JsonResponder::notFound('Route Not Found');

            case "Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException":
                return JsonResponder::methodNotAllowed('Method Not Allowed');
    
            case "Illuminate\Database\Eloquent\MethodNotAllowedHttpException":
                return JsonResponder::methodNotAllowed($exception->getMessage());

            case "Illuminate\Database\Eloquent\ModelNotFoundException":
                return JsonResponder::notFound('Resource Not Found');

            case "Illuminate\Auth\AuthenticationException":
                return JsonResponder::unauthenticated($exception->getMessage());

            case "App\Exceptions\UnauthorizedException":
                return JsonResponder::unauthenticated($exception->getMessage());

            case "App\Exceptions\PrinterNotFoundException":
                return JsonResponder::notFound($exception->getMessage());

            case "Tymon\JWTAuth\Exceptions\TokenInvalidException":
                return JsonResponder::unauthenticated($exception->getMessage());

            case "Tymon\JWTAuth\Exceptions\TokenExpiredException":
                return JsonResponder::unauthenticated($exception->getMessage());

            case "Tymon\JWTAuth\Exceptions\JWTException":
                return JsonResponder::unauthenticated($exception->getMessage());

            case "Illuminate\Validation\ValidationException":
                return JsonResponder::validationError('Validation Failed', $exception->errors());

            case "Spatie\Permission\Exceptions\UnauthorizedException":
                return JsonResponder::forbidden('User does not have the right permissions.');

            default:
                info($exception);
                return JsonResponder::internalServerError();
        }
    }
}
