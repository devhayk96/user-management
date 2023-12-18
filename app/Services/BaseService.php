<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class BaseService
{
    /**
     * @param string $message
     * @param array $result
     * @param int $code
     * @return JsonResponse
     */
    public function sendResponse(
        string $message,
        mixed $result = [],
        int $code = 200,
    ): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];
        if ($result) {
            $response['data'] = $result;
        }
        return response()->json($response, $code);
    }

    /**
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    public function sendError(
        $error,
        array $errorMessages = [],
        int $code = 404
    ): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return Response::json($response, $code);
    }

}
