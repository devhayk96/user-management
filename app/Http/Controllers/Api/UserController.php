<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{

    /**
     * @OA\Info(
     *    title="Swagger with Laravel",
     *    version="1.0.0",
     * )
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get logged-in user details",
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getProfile(Request $request): JsonResponse
    {
        return Response::json(['user' => $request->user()]);
    }
}
