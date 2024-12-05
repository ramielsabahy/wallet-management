<?php

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs in a user successfully', function () {

    $password = 'securepassword';
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make($password),
    ]);

    $authService = app(AuthService::class);

    $response = $authService->login([
        'email' => $user->email,
        'password' => $password,
    ]);

    expect($response)->toBeArray()
        ->and($response['user'])->toBeInstanceOf(\App\Http\Resources\API\UserResource::class)
        ->and($response['token'])->toBeString();
});

it('fails to log in with invalid credentials', function () {

    $password = 'securepassword';
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make($password),
    ]);

    $authService = app(AuthService::class);

    $response = $authService->login([
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    expect($response)->toBeFalse();
});

it('registers a new user successfully', function () {

    $data = [
        'name' => 'Test User',
        'email' => 'newuser@example.com',
        'password' => 'securepassword',
    ];

    $authService = app(AuthService::class);

    $response = $authService->register($data);

    expect($response)->toBeArray()
        ->and($response['user'])->toBeInstanceOf(\App\Http\Resources\API\UserResource::class)
        ->and($response['token'])->toBeString();

    $this->assertDatabaseHas('users', [
        'email' => $data['email'],
    ]);
});

it('throws an exception when registration data is invalid', function () {
    $data = [
        'email' => 'invaliduser@example.com'
    ];

    $authService = app(AuthService::class);

    expect(fn () => $authService->register($data))->toThrow(\Illuminate\Database\QueryException::class);
});
