<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function displayParameters(Request $request): JsonResponse
    {
        echo "You can take these values and use them for API calls\n";

        return response()->json(
            $request->all(),
            options: JSON_PRETTY_PRINT
        );
    }

    public function displayVerifySegments(Request $request): JsonResponse
    {
        echo "You can take these values and use them for mail verification API calls \n";

        $values = [
            'userId' => $request->segment(3),
            'hash' => $request->segment(4),
        ];

        if ($code = $request->get('code')) {
            $values['code'] = $code;
        }

        return response()->json(
            $values,
            options: JSON_PRETTY_PRINT
        );
    }


    /**
     * @OA\Info(
     *    title="Swagger with Laravel",
     *    version="1.0.0",
     * )
     * @OA\SecurityScheme(
     *     type="http",
     *     securityScheme="bearerAuth",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )
     */


}
