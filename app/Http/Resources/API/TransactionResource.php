<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'type'              => $this->type,
            'amount'            => $this->amount,
            'fees'              => $this->fee,
            'balance_after'     => $this->final_balance,
            'related_wallet'    => isset($this->related_wallet_id) ? new UserResource($this->relatable->user) : null,
        ];
    }
}
