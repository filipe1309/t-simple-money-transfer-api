<?php

namespace App\Http\Controllers\V1;

use App\Events\NewTransactionCreatedEvent;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessTransactionJob;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function create(Request $request)
    {
        $payer_id = $request->input('payer');
        $payee_id = $request->input('payee');
        $value = $request->input('value');

        $payer_wallet_id = Wallet::where('user_id', $payer_id)->first()->id;
        $payee_wallet_id = Wallet::where('user_id', $payee_id)->first()->id;
        $transaction_id = Uuid::uuid4()->toString();

        $transaction = Transaction::create(
            [
                'id' => $transaction_id,
                'payer_wallet_id' => $payer_wallet_id,
                'payee_wallet_id' => $payee_wallet_id,
                'value' => $value,
                'processed' => false
            ]
        )->refresh();

        dispatch(new ProcessTransactionJob($transaction))
            ->onQueue('transactionJobQueue');

        event(new NewTransactionCreatedEvent($transaction));

        return json_encode(['status' => true, 'id' => $transaction_id]);
    }
}
