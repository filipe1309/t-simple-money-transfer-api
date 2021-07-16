<?php

namespace App\Http\Validators;

use App\Rules\PayerHasEnoughBalance;
use App\Rules\PayerIsACommonUser;
use App\Rules\ValueIsCorrect;
use Illuminate\Http\Request;

trait ValidatesTransactionRequest
{

    /**
     * @param Request $request
     */
    protected function validateCreateRequest(Request $request)
    {
        $this->validate($request, [
            'payer' => ['required', 'uuid', 'exists:wallets,id'], //, new PayerIsACommonUser, new PayerHasEnoughBalance($request->input('value'))
            'payee' => ['required', 'uuid', 'exists:wallets,id', 'different:payer'],
            'value' => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'], //, new ValueIsCorrect
        ]);
    }
}
