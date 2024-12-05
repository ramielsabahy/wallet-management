<?php

namespace App\Services;

use App\Http\Resources\API\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $data)
    {
        $loginStatus = Auth::attempt($data);
        if ($loginStatus) {
            $user = Auth::user();
            return [
                'user' => new UserResource($user),
                'token' => $user->createToken('auth_token')->plainTextToken
            ];
        }
        return false;
    }

    public function register(array $data)
    {
        $user = User::create($data);
        return [
            'user' => new UserResource($user),
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }
}
