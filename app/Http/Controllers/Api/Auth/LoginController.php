<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     * )
     */

    public function __construct(
        protected AuthService $authService
    ) {

    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Authenticate user and generate JWT token",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string", format = "password")
     *     ),
     *     @OA\Response(response="200", description="Login successful", @OA\JsonContent()),
     *     @OA\Response(response="401", description="Invalid credentials", @OA\JsonContent()),
     *     @OA\Response(response="422", description="Validation errors", @OA\JsonContent())
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->attemptLogin($request->only(['email', 'password']));
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Sign out user and delete JWT token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="You are logged out.", @OA\JsonContent())
     * )
     */
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }

    /**
     * @OA\Post(
     *      path="/api/oauth/refresh-token",
     *      tags={"Auth"},
     *      summary="Refresh Access Token",
     *      description="Refresh the user's access token.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Token refreshed successfully", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthorized - Invalid or expired refresh token"),
     * )
     * @throws \Exception
     */
    public function refreshToken(): JsonResponse
    {
        return $this->authService->refreshAccessToken();
    }

}
