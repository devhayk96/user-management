<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
     *     @OA\Response(response="200", description="Success", @OA\JsonContent()),
     *     security={{"bearerAuth":{}}},
     *     tags={"User"}
     * )
     */
    public function getProfile(Request $request): JsonResponse
    {
        return Response::json(['user' => new UserResource($request->user())]);
    }
}
