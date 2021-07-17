<?php

namespace App\Http\Controllers\V1;

use App\Helpers\OrderByHelper;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private array $searchFields = ['full_name', 'email', 'shopkeeper', 'registration_number'];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private UserService $service
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
     * @param Exception $e
     * @param integer $statusCode
     * @return array
     */
    protected function errorResponse(Exception $e, int $statusCode = Response::HTTP_BAD_REQUEST): array
    {
        return [
            'status_code' => $statusCode,
            'message' => $e->getMessage()
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
            $orderBy = OrderByHelper::treatOrderBy($request->get('order_by', ''));
            $searchString = $request->get('q', '');

            if (!empty($searchString)) {
                $result = $this->service->searchBy($searchString, $this->searchFields, $limit, $orderBy);
            } else {
                $result = $this->service->findAll($limit, $orderBy);
            }

            $response = $this->successResponse($result, Response::HTTP_PARTIAL_CONTENT);
        } catch (Exception $e) {
            $response = $this->errorResponse($e);
        }

        return response()->json($response, $response['status_code']);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function findOneBy(string $id): JsonResponse
    {
        try {
            $result = $this->service->findOneBy($id);
            $response = $this->successResponse($result);
        } catch (Exception $e) {
            $response = $this->errorResponse($e);
        }

        return response()->json($response, $response['status_code']);
    }
}
