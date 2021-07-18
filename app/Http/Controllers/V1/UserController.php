<?php

namespace App\Http\Controllers\V1;

use App\Helpers\OrderByHelper;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private UserService $service,
        private OrderByHelper $orderByHelper
    ) {
    }

    /**
     * @param array $data
     * @param integer $statusCode
     * @return array
     */
    protected function successResponse(array $data, int $statusCode = Response::HTTP_OK): array
    {
        return [
            'status_code' => $statusCode,
            'data' => $data
        ];
    }

    /**
     * @param Exception $exception
     * @param integer $statusCode
     * @return array
     */
    protected function errorResponse(Exception $exception, int $statusCode = Response::HTTP_BAD_REQUEST): array
    {
        return [
            'status_code' => $statusCode,
            'message' => $exception->getMessage()
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findAll(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->get('limit', 10);
            $orderBy = $this->orderByHelper->treatOrderBy($request->get('order_by', ''));

            $result = $this->service->findAll($limit, $orderBy);

            $response = $this->successResponse($result, Response::HTTP_PARTIAL_CONTENT);
        } catch (Exception $e) {
            $response = $this->errorResponse($e);
        }

        return response()->json($response, $response['status_code']);
    }

    /**
     * @param string $userId
     * @return JsonResponse
     */
    public function findOneBy(string $userId): JsonResponse
    {
        try {
            $result = $this->service->findOneBy($userId);
            $response = $this->successResponse($result);
        } catch (Exception $e) {
            $response = $this->errorResponse($e);
        }

        return response()->json($response, $response['status_code']);
    }
}
