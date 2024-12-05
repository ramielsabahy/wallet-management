<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferBalanceRequest;
use App\Http\Requests\WalletTopUpRequest;
use App\Http\Requests\WithdrawRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends BaseAPIController
{
    public function __construct(public TransactionService $transactionService)
    {

    }

    public function topUp(WalletTopUpRequest $request): JsonResponse
    {
        $result = $this->transactionService->topUp(amount: $request->amount, user: auth()->user());
        if ($result) {
            return $this->success((object)[]);
        }
        return $this->error(__("errors.something_went_wrong"));
    }

    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        $result = $this->transactionService->withdraw(user: auth()->user(), amount: $request->amount);
        if ($result) {
            return $this->success((object)[]);
        }
        return $this->error(__("errors.something_went_wrong"));
    }

    public function transfer(TransferBalanceRequest $request): JsonResponse
    {
        $transfer = $this->transactionService->transfer(auth()->user(), data: $request->validated());
        if (!$transfer) {
            return $this->error(__("errors.something_went_wrong"));
        }
        return $this->success((object)[]);
    }

    public function list(): JsonResponse
    {
        $data = $this->transactionService->list(user: auth()->user());
        return $this->success($data);
    }
}
