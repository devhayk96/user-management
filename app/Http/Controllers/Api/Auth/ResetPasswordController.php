<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/password/reset",
     *     tags={"Authentication"},
     *     summary="Send password reset link",
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="Reset token",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
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
     *         description="User's new password",
     *         required=true,
     *         @OA\Schema(type="string", format = "password")
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="User's confirm new password",
     *         required=true,
     *         @OA\Schema(type="string", format = "password")
     *     ),
     *     @OA\Response(response="201", description="Password successfully reset!", @OA\JsonContent()),
     *     @OA\Response(response="422", description="Validation errors", @OA\JsonContent())
     * )
     */
    public function __invoke(ResetPasswordRequest $request, AuthService $authService): JsonResponse
    {
        return $authService->resetPassword($request->only('email', 'password', 'password_confirmation', 'token'));
    }
}
