<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});
Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'wallet'], function () {
    Route::post('top-up', [TransactionController::class, 'topUp']);
    Route::post('withdraw', [TransactionController::class, 'withdraw']);
    Route::post('transfer', [TransactionController::class, 'transfer']);
    Route::get('transactions', [TransactionController::class, 'list']);
});
