<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {

    }

    /**
     * @OA\Post(
     *     path="/api/login",
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
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->attemptLogin($request->only(['email', 'password']));
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Sign out user and delete JWT token",
     *     @OA\Response(response="200", description="You are logged out.")
     * )
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }
}
