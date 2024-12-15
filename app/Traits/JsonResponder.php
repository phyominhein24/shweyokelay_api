<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponder
{
    public static function respond($message, $status, $data = []): JsonResponse
    {
        $responseBody = collect(['message' => $message, 'data' => $data]);

        return new JsonResponse($responseBody, $status);
    }

    public static function success($message = 'Success', $data = []): JsonResponse
    {
        return self::respond($message, 200, $data);
    }

    public static function unauthenticated($message = 'Unauthenticated'): JsonResponse
    {
        return self::respond($message, 401);
    }

    public static function unauthorized($message = 'Unauthorized'): JsonResponse
    {
        return self::respond($message, 401);
    }

    public static function forbidden($message = 'Forbidden'): JsonResponse
    {
        return self::respond($message, 403);
    }

    public static function validationError($message, $data)
    {
        return self::respond($message, 422, $data);
    }

    public static function internalServerError($message = 'Internal Server Error', $data = []): JsonResponse
    {
        return self::respond($message, 500, $data);
    }

    public static function notFound($message = 'Not Found')
    {
        return self::respond($message, 404);
    }

    public static function methodNotAllowed($message = 'The current method not allow for this route')
    {
        return self::respond($message, 405);
    }

    public static function noContent($message = 'No Content')
    {
        return self::respond($message, 204);
    }

    public static function tooManyAttempts(): JsonResponse
    {
        return self::respond('Too many attempts, try again later', 429);
    }
}
