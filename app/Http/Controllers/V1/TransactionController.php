<?php

namespace App\Http\Controllers\V1;

use App\Contracts\TransactionServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Validators\ValidatesTransactionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TransactionController extends Controller
{
    use ValidatesTransactionRequest;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private TransactionServiceInterface $service
    ) {
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $this->validateCreateRequest($request);
            $transaction = $this->service->create($request->all());
            $response = ['status' => true, 'id' => $transaction['id']];
            $statusCode = Response::HTTP_CREATED;
        } catch (Throwable $e) {
            $response = ['message' => $e->getMessage()];
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return response()->json($response, $statusCode);
    }
}
