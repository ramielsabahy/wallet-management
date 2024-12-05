<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['id'];



    public function relatable()
    {
        return $this->belongsTo(Wallet::class, 'related_wallet_id');
    }
}
