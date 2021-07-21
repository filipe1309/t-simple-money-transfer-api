<?php

namespace App\Services;

use App\Contracts\ExternalAuthorizerServiceInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ExternalAuthorizerService implements ExternalAuthorizerServiceInterface
{
    public function authorize(array $transaction): bool
    {
        $response = Http::post(env('EXTERNAL_AUTHORIZER_URL'), $transaction);

        return $this->isAuthorized($response->json(), $response->status());
    }

    private function isAuthorized(array $responseBody, int $responseStatus): bool
    {
        return $responseBody['message'] === env('EXTERNAL_AUTHORIZER_MESSAGE') && $responseStatus === Response::HTTP_OK;
    }
}
