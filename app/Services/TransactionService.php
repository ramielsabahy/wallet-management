<?php

namespace App\Services;

use App\Enums\TransactionTypeEnum;
use App\Http\Resources\API\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use App\Strategies\FeeCalculator;
use App\Strategies\NoFeeStrategy;
use App\Strategies\StandardFeeStrategy;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * @param int $amount
     * @param User $user
     * @return bool
     */
    public function topUp(int $amount, User|Authenticatable $user): bool
    {
        DB::beginTransaction();
        try {
            $user->wallet()->increment('balance', amount: $amount);
            $this->log($user->wallet->id, TransactionTypeEnum::DEPOSIT->value, $amount, $user->wallet->balance);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
        return true;
    }

    public function transfer(User|Authenticatable $fromUser, array $data): bool
    {
        DB::beginTransaction();
        try {
            $toUser = User::whereEmail(Arr::get($data, 'email'))->first();
            $amount = Arr::get($data, 'amount');

            $feeCalculator = new FeeCalculator(
                $amount > 25 ? new StandardFeeStrategy() : new NoFeeStrategy()
            );
            $fee = $feeCalculator->calculateFee($amount);
            $totalAfterFees = $amount + $fee;
            if ($amount > $fromUser->wallet->balance) {
                return false;
            }
            $fromUser->wallet()->decrement('balance', amount: $totalAfterFees);
            $toUser->wallet()->increment('balance', amount: $amount);

            $this->log($fromUser->wallet->id, TransactionTypeEnum::OUTGOING_TRANSFER->value, $amount, $fromUser->wallet->balance - $totalAfterFees, $fee, $toUser->wallet->id);
            $this->log($toUser->wallet->id, TransactionTypeEnum::INCOMING_TRANSFER->value, $amount, $toUser->wallet->balance, $fee, $fromUser->wallet->id);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
        return true;
    }

    public function list(User|Authenticatable $user): array
    {
        $transactionsPagination = $user->wallet->transactions()->orderBy('id', 'DESC')->paginate(10);
        $transactions = TransactionResource::collection($transactionsPagination->items());
        return [
            'balance'       => $user->wallet->balance,
            'transactions'  => $transactions,
            'pagination'    => [
                'total' => $transactionsPagination->total(),
                'per_page' => $transactionsPagination->perPage(),
                'current_page' => $transactionsPagination->currentPage(),
                'last_page' => $transactionsPagination->lastPage(),
            ]
        ];
    }

    private function log(
        int $walletId,
        string $type,
        float $amount,
        float $finalBalance,
        float $fees = 0,
        int $relatedWalletId = null
    ): bool
    {
        Transaction::create([
            'wallet_id' => $walletId,
            'type' => $type,
            'amount' => $amount,
            'final_balance' => $finalBalance,
            'fee' => $fees,
            'related_wallet_id' => $relatedWalletId
        ]);
        return true;
    }
}
