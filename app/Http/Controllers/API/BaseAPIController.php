<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseAPIController extends Controller
{
    public function success(mixed $data, int $httpCode = Response::HTTP_OK): JsonResponse
    {
        return APIResponse::success($data, $httpCode);
    }


    public function error(string $message, array $errors = [], int $httpCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return APIResponse::error($message, $errors, $httpCode);
    }
}
