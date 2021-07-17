<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    const PROCESSED = 1;

    protected $fillable = [
        'id', 'payer_wallet_id', 'payee_wallet_id', 'value', 'processed'
    ];

    public $incrementing = false;

    public function payer(): HasOne
    {
        return $this->hasOne(Wallet::class, 'id', 'payer_wallet_id');
    }

    public function payee(): HasOne
    {
        return $this->hasOne(Wallet::class, 'id', 'payee_wallet_id');
    }
}
