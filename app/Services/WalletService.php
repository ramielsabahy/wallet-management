<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;

class WalletService
{
    public function create(User $user)
    {
        return $user->wallet()->create();
    }
}
