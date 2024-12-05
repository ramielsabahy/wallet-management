<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseAPIController
{
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $loginData = $authService->login(data: $request->validated());
        if (!$loginData) {
            return $this->error(__("auth.invalid_credentials"));
        }
        return $this->success($loginData);
    }

    public function register(RegisterRequest $request, AuthService $authService): JsonResponse
    {
        $registrationData = $authService->register(data: $request->validated());
        if (!$registrationData) {
            return $this->error(__("auth.something_went_wrong"));
        }
        return $this->success($registrationData);
    }
}
