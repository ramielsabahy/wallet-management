<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class APIResponse
{
    public static function success(mixed $data, int $httpCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['data' => $data], $httpCode);
    }

    public static function error(string $message, array $errors, int $httpCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(
            [
                'message' => $message,
                'errors' => $errors,
            ], $httpCode);
    }
}
