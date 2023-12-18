<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/password/forgot",
     *     tags={"Authentication"},
     *     summary="Send password reset link",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="You should get recovery e-mail shortly.", @OA\JsonContent()),
     *     @OA\Response(response="400", description="Failed to send email. Please check mail credentials has been set correctly", @OA\JsonContent()),
     *     @OA\Response(response="422", description="Validation errors", @OA\JsonContent())
     * )
     * @throws Exception
     */
    public function __invoke(ForgotPasswordRequest $request, AuthService $authService): JsonResponse
    {
        return $authService->sendPasswordResetLink($request->only('email'));
    }
}
