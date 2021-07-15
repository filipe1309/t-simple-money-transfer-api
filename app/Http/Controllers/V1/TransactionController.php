<?php

namespace App\Http\Controllers\V1;

use App\Events\NewTransactionCreatedEvent;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessTransactionJob;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private TransactionService $service
    ) {
    }

    public function create(Request $request)
    {
        $transaction = $this->service->create($request->all());
        return json_encode(['status' => true, 'id' => $transaction['id']]);
    }
}
