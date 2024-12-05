<?php

use App\Enums\TransactionTypeEnum;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('can top up a wallet balance successfully', function () {
    $user = User::factory()->has(Wallet::factory())->create();
    $transactionService = app(TransactionService::class);

    $amount = 1000;
    $result = $transactionService->topUp($amount, $user);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('wallets', [
        'id' => $user->wallet->id,
        'balance' => $amount,
    ]);
    $this->assertDatabaseHas('transactions', [
        'wallet_id' => $user->wallet->id,
        'type' => \App\Enums\TransactionTypeEnum::DEPOSIT->value,
        'amount' => $amount,
    ]);
});


it('can transfer amount successfully', function () {
    $fromUser = User::factory()->create();
    $toUser = User::factory()->create();
    $fromUser->wallet()->update(['balance' => 1000]);

    $transactionService = app(TransactionService::class);
    $data = ['email' => $toUser->email, 'amount' => 100];

    $result = $transactionService->transfer($fromUser, $data);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('wallets', [
        'id' => $fromUser->wallet->id,
        'balance' => 897.5, // 100 + 2.5 fee
    ]);
    $this->assertDatabaseHas('wallets', [
        'id' => $toUser->wallet->id,
        'balance' => 100,
    ]);
    $this->assertDatabaseHas('transactions', [
        'wallet_id' => $fromUser->wallet->id,
        'type' => TransactionTypeEnum::OUTGOING_TRANSFER->value,
        'amount' => 100,
    ]);
    $this->assertDatabaseHas('transactions', [
        'wallet_id' => $toUser->wallet->id,
        'type' => TransactionTypeEnum::INCOMING_TRANSFER->value,
        'amount' => 100,
    ]);
});

it('returns false when transfer amount exceeds balance', function () {
    $fromUser = User::factory()->has(Wallet::factory(['balance' => 100]))->create();
    $toUser = User::factory()->has(Wallet::factory())->create();

    $transactionService = app(TransactionService::class);
    $data = ['email' => $toUser->email, 'amount' => 200];

    $result = $transactionService->transfer($fromUser, $data);

    expect($result)->toBeFalse();
});

it('can withdraw amount successfully', function () {
    $user = User::factory()->create();
    $user->wallet()->update(['balance' => 1000]);
    $transactionService = app(TransactionService::class);

    $amount = 500;
    $result = $transactionService->withdraw($user, $amount);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('wallets', [
        'id' => $user->wallet->id,
        'balance' => 500,
    ]);
    $this->assertDatabaseHas('transactions', [
        'wallet_id' => $user->wallet->id,
        'type' => TransactionTypeEnum::WITHDRAWAL->value,
        'amount' => $amount,
    ]);
});

it('lists transactions for a user', function () {
    $user = User::factory()->has(Wallet::factory())->create();
    Transaction::factory()->count(5)->create([
        'wallet_id' => $user->wallet->id,
    ]);

    $transactionService = app(TransactionService::class);
    $response = $transactionService->list($user);

    expect($response)->toBeArray()
        ->and($response['balance'])->toBe($user->wallet->balance)
        ->and($response['transactions']->count())->toBe(5)
        ->and($response['pagination'])->toMatchArray([
            'total' => 5,
            'per_page' => 10,
            'current_page' => 1,
            'last_page' => 1,
        ]);
});
